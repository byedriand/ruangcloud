/* Helper bersama (dipakai juga ruangcloud.js lewat ekspor `api`). */
const notify = (message) => window.notify?.(message);

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

export async function api(url, { method = 'GET', body, json, signal } = {}) {
    const headers = { Accept: 'application/json', 'X-CSRF-TOKEN': csrf() };
    if (json) {
        headers['Content-Type'] = 'application/json';
        body = JSON.stringify(json);
    }

    const response = await fetch(url, { method, headers, body, signal, credentials: 'same-origin' });
    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const error = new Error(data.message || `Permintaan gagal (HTTP ${response.status})`);
        error.status = response.status;
        throw error;
    }
    return data;
}

const formatBytes = (n) => {
    const units = ['B', 'KB', 'MB', 'GB'];
    let i = 0;
    while (n >= 1024 && i < units.length - 1) {
        n /= 1024;
        i += 1;
    }
    return `${n.toFixed(i === 0 ? 0 : 1).replace('.', ',')} ${units[i]}`;
};

/* ------------------------------------------------------------------
   Pengunggah chunk resumable — MULTI FILE.
   Banyak unggahan bisa berjalan bersamaan; tiap unggahan adalah "job"
   dengan kartu mini sendiri di pojok layar (bertumpuk sebagai daftar)
   dan baris sendiri di tabel halaman Download. Semua job tersimpan di
   localStorage + file-nya di IndexedDB, jadi pindah halaman tidak
   membatalkan unggahan mana pun.
------------------------------------------------------------------- */

const CHUNK_FALLBACK = 5 * 1024 * 1024; // 5 MB bila server tak memberi tahu
const MAX_SIZE = 5 * 1024 ** 3;         // 5 GB
const MAX_RETRY = 5;
const REG_KEY = 'rc_active_uploads';
const LEGACY_KEY = 'rc_active_upload';
const IDB_NAME = 'ruangcloud';
const IDB_STORE = 'upload-files';

const $ = (selector, root = document) => root.querySelector(selector);

/* ---------------- Registry (localStorage) ---------------- */
const jobs = new Map(); // cid -> job
let cidSeq = 0;
const newCid = () => `u${Date.now().toString(36)}${(cidSeq += 1)}`;

function saveReg() {
    try {
        const entries = [...jobs.values()]
            .filter((job) => job.sessionId)
            .map((job) => ({
                cid: job.cid, sessionId: job.sessionId, fingerprint: job.fingerprint,
                name: job.name, size: job.size, mime: job.mime, lastModified: job.lastModified,
                folderId: job.folderId, paused: job.paused,
                chunkSize: job.chunkSize, totalChunks: job.totalChunks,
            }));
        localStorage.setItem(REG_KEY, JSON.stringify(entries));
    } catch { /* abaikan */ }
}

function loadReg() {
    let entries = [];
    try { entries = JSON.parse(localStorage.getItem(REG_KEY)) || []; } catch { entries = []; }
    // Migrasi format lama (satu unggahan).
    try {
        const legacy = JSON.parse(localStorage.getItem(LEGACY_KEY));
        if (legacy?.sessionId) { entries.push({ ...legacy, cid: newCid() }); localStorage.removeItem(LEGACY_KEY); }
    } catch { /* abaikan */ }
    return entries;
}

/* ---------------- IndexedDB: simpan blob tiap sesi ---------------- */
function idbOpen() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(IDB_NAME, 1);
        req.onupgradeneeded = () => req.result.createObjectStore(IDB_STORE);
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}
async function idbPut(key, blob) {
    try {
        const db = await idbOpen();
        await new Promise((resolve, reject) => {
            const tx = db.transaction(IDB_STORE, 'readwrite');
            tx.objectStore(IDB_STORE).put(blob, key);
            tx.oncomplete = resolve; tx.onerror = () => reject(tx.error);
        });
    } catch { /* IndexedDB tak tersedia — pulih akan minta file dipilih lagi */ }
}
async function idbGet(key) {
    try {
        const db = await idbOpen();
        return await new Promise((resolve, reject) => {
            const req = db.transaction(IDB_STORE).objectStore(IDB_STORE).get(key);
            req.onsuccess = () => resolve(req.result || null);
            req.onerror = () => reject(req.error);
        });
    } catch { return null; }
}
async function idbDel(key) {
    try {
        const db = await idbOpen();
        await new Promise((resolve) => {
            const tx = db.transaction(IDB_STORE, 'readwrite');
            tx.objectStore(IDB_STORE).delete(key);
            tx.oncomplete = resolve; tx.onerror = resolve;
        });
    } catch { /* abaikan */ }
}

/* ---------------- Util ---------------- */
async function fingerprintOf(file) {
    const text = `${file.name}|${file.size}|${file.lastModified}`;
    try {
        const digest = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(text));
        return [...new Uint8Array(digest)].map((b) => b.toString(16).padStart(2, '0')).join('');
    } catch {
        return text;
    }
}

function currentFolderId() {
    const params = new URLSearchParams(window.location.search);
    const folder = params.get('folder');
    return folder ? Number(folder) : null;
}

function formatEta(seconds) {
    if (!Number.isFinite(seconds) || seconds < 0) return '';
    if (seconds < 60) return `tersisa ${Math.ceil(seconds)} dtk`;
    return `tersisa ${Math.ceil(seconds / 60)} menit`;
}

/* ---------------- UI per job ---------------- */
const onDownloadsPage = () => !!$('#dl-table-head');

function syncCardVisibility() {
    const card = $('#upload-card');
    if (!card) return;
    const count = $('#upload-count');
    if (count) count.textContent = jobs.size ? `${jobs.size} file` : '';
    card.hidden = jobs.size === 0 || onDownloadsPage();
}

function cardItemHtml() {
    return `
      <div class="flex items-center gap-3 px-4 py-3">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
          <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4m0 0 4 4m-4-4L8 8"/><path d="M4 16v2.5A1.5 1.5 0 0 0 5.5 20h13a1.5 1.5 0 0 0 1.5-1.5V16"/></svg>
        </div>
        <div class="min-w-0 flex-1">
          <div class="flex items-center justify-between gap-2">
            <p class="ju-name truncate text-[13px] font-bold text-slate-900">-</p>
            <span class="ju-status shrink-0 text-xs font-extrabold text-emerald-600">0%</span>
          </div>
          <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100">
            <div class="ju-bar upload-progress h-full rounded-full bg-emerald-500 transition-all" style="width: 0%"></div>
          </div>
          <p class="ju-meta mt-1 truncate text-[11px] text-slate-500"></p>
        </div>
        <button type="button" data-action="toggle-upload" aria-label="Jeda / lanjutkan" class="shrink-0 rounded-lg p-1.5 text-slate-500 hover:bg-slate-100">
          <span class="ju-icon-pause"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 5v14M15 5v14"/></svg></span>
          <span class="ju-icon-resume" hidden><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4m0 0 4 4m-4-4L8 8"/><path d="M4 16v2.5A1.5 1.5 0 0 0 5.5 20h13a1.5 1.5 0 0 0 1.5-1.5V16"/></svg></span>
        </button>
        <button type="button" data-action="cancel-upload" aria-label="Batalkan" class="shrink-0 rounded-lg p-1.5 text-slate-500 hover:bg-slate-100">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
      </div>`;
}

function ensureUI(job) {
    // Baris di tabel halaman Download (dari <template>).
    const tpl = $('#upload-row-template');
    if (tpl && !document.querySelector(`.ju-row[data-ju="${job.cid}"]`)) {
        const row = tpl.content.firstElementChild.cloneNode(true);
        row.classList.add('ju-row');
        row.dataset.ju = job.cid;
        row.hidden = false;
        row.querySelectorAll('[data-action]').forEach((btn) => { btn.dataset.cid = job.cid; });
        const rows = document.querySelectorAll('.ju-row');
        (rows[rows.length - 1] || $('#dl-table-head'))?.after(row);
    }
    // Item mini di kartu pojok (di luar halaman Download).
    const items = $('#upload-items');
    if (items && !onDownloadsPage() && !items.querySelector(`[data-ju="${job.cid}"]`)) {
        const item = document.createElement('div');
        item.dataset.ju = job.cid;
        item.innerHTML = cardItemHtml();
        item.querySelectorAll('[data-action]').forEach((btn) => { btn.dataset.cid = job.cid; });
        items.appendChild(item);
    }
    syncCardVisibility();
    paint(job);
}

function removeUI(job) {
    document.querySelectorAll(`[data-ju="${job.cid}"]`).forEach((el) => el.remove());
    syncCardVisibility();
}

function paint(job) {
    const percent = job.totalChunks ? Math.round((job.received.size / job.totalChunks) * 100) : 0;
    document.querySelectorAll(`[data-ju="${job.cid}"]`).forEach((box) => {
        const name = box.querySelector('.ju-name'); if (name) name.textContent = job.name;
        const size = box.querySelector('.ju-size'); if (size) size.textContent = formatBytes(job.size);
        const bar = box.querySelector('.ju-bar');
        if (bar) {
            bar.style.width = `${percent}%`;
            bar.classList.toggle('upload-progress', job.state === 'uploading');
        }
        const status = box.querySelector('.ju-status');
        if (status) {
            status.textContent = job.state === 'paused' ? 'Dijeda'
                : job.state === 'needs-file' ? 'Terhenti'
                : job.state === 'done' ? 'Selesai'
                : job.state === 'error' ? 'Gagal'
                : `${percent}%`;
        }
        const meta = box.querySelector('.ju-meta'); if (meta && job.meta) meta.textContent = job.meta;
        const label = box.querySelector('.ju-toggle-label');
        if (label) label.textContent = (job.paused || job.state === 'needs-file') ? 'Lanjutkan' : 'Jeda';
        const iconPause = box.querySelector('.ju-icon-pause'); if (iconPause) iconPause.hidden = job.paused || job.state === 'needs-file';
        const iconResume = box.querySelector('.ju-icon-resume'); if (iconResume) iconResume.hidden = !(job.paused || job.state === 'needs-file');
    });
}

function setPaused(job, paused) {
    job.paused = paused;
    job.state = paused ? 'paused' : 'uploading';
    if (paused) job.meta = 'Unggahan dijeda';
    saveReg();
    paint(job);
}

/* ---------------- Siklus job ---------------- */
function destroyJob(job) {
    jobs.delete(job.cid);
    if (job.sessionId) idbDel(job.sessionId);
    saveReg();
    removeUI(job);
}

async function attachSession(job) {
    const init = await api('/upload/init', {
        method: 'POST',
        json: {
            name: job.name, size: job.size, mime: job.mime || 'application/octet-stream',
            last_modified: job.lastModified, fingerprint: job.fingerprint, folder_id: job.folderId,
        },
    });
    job.sessionId = init.session_id;
    job.chunkSize = init.chunk_size || CHUNK_FALLBACK;
    job.totalChunks = init.total_chunks;
    job.received = new Set(init.received || []);
    if (job.file) await idbPut(job.sessionId, job.file);
    saveReg();
    paint(job);
}

export async function startUpload(file) {
    if (!file) return;
    if (file.size === 0) return notify('File kosong tidak dapat diunggah');
    if (file.size > MAX_SIZE) return notify('Maksimum 5 GB per file');

    const fingerprint = await fingerprintOf(file);
    if ([...jobs.values()].some((job) => job.fingerprint === fingerprint)) {
        return notify(`"${file.name}" sudah ada di antrean unggahan`);
    }

    const job = {
        cid: newCid(), sessionId: null, file, fingerprint,
        name: file.name, size: file.size, mime: file.type, lastModified: file.lastModified,
        folderId: currentFolderId(), paused: false, cancelled: false,
        received: new Set(), chunkSize: CHUNK_FALLBACK, totalChunks: 0,
        state: 'uploading', meta: 'Menyiapkan unggahan…', controller: null,
        speed: { lastTime: 0, lastReceived: 0, ema: 0 },
    };
    jobs.set(job.cid, job);
    ensureUI(job);

    try {
        await attachSession(job);
    } catch (error) {
        job.state = 'error'; job.meta = error.message || 'Gagal memulai unggahan';
        paint(job); notify(job.meta);
        setTimeout(() => destroyJob(job), 3000);
        return;
    }
    runJob(job);
}

async function sha256Hex(blob) {
    const digest = await crypto.subtle.digest('SHA-256', await blob.arrayBuffer());
    return [...new Uint8Array(digest)].map((b) => b.toString(16).padStart(2, '0')).join('');
}

function waitIfPaused(job) {
    if (!job.paused) return Promise.resolve();
    return new Promise((resolve) => {
        const timer = setInterval(() => {
            if (!job.paused || job.cancelled) { clearInterval(timer); resolve(); }
        }, 300);
    });
}

function paintProgress(job) {
    const now = performance.now();
    const sp = job.speed;
    if (sp.lastTime) {
        const dt = (now - sp.lastTime) / 1000;
        const dBytes = (job.received.size - sp.lastReceived) * job.chunkSize;
        if (dt > 0 && dBytes >= 0) {
            const rate = dBytes / dt;
            sp.ema = sp.ema ? sp.ema * 0.65 + rate * 0.35 : rate;
        }
    }
    sp.lastTime = now;
    sp.lastReceived = job.received.size;

    const sentBytes = Math.min(job.size, job.received.size * job.chunkSize);
    let meta = `${formatBytes(sentBytes)} dari ${formatBytes(job.size)}`;
    if (sp.ema > 0 && job.state === 'uploading') {
        const eta = formatEta((job.size - sentBytes) / sp.ema);
        meta = `${formatBytes(sp.ema)}/dtk${eta ? ` • ${eta}` : ''} • ${meta}`;
    }
    job.meta = meta;
    paint(job);
}

async function runJob(job) {
    // Segarkan daftar chunk dari server (melanjutkan sesi lama).
    try {
        const status = await api(`/upload/${job.sessionId}`);
        if (status.status !== 'uploading') {
            if (status.status === 'completed') return completeJob(job, false);
            throw new Error(status.error || 'Sesi unggahan tidak aktif');
        }
        job.received = new Set(status.received || [...job.received]);
    } catch (error) {
        if (error.status === 410 || error.status === 403) { destroyJob(job); return; }
    }
    paintProgress(job);

    while (job.received.size < job.totalChunks) {
        await waitIfPaused(job);
        if (job.cancelled) return;

        let index = 0;
        while (job.received.has(index)) index += 1;
        const start = index * job.chunkSize;
        const chunk = job.file.slice(start, Math.min(start + job.chunkSize, job.size));
        const checksum = await sha256Hex(chunk);

        let attempt = 0;
        for (;;) {
            await waitIfPaused(job);
            if (job.cancelled) return;
            job.controller = new AbortController();
            try {
                const form = new FormData();
                form.append('index', String(index));
                form.append('checksum', checksum);
                form.append('chunk', chunk, `chunk-${index}`);
                await api(`/upload/${job.sessionId}/chunk`, { method: 'POST', body: form, signal: job.controller.signal });
                break;
            } catch (error) {
                if (job.cancelled || error.name === 'AbortError') return;
                if (error.status === 410 || error.status === 403) {
                    notify('Sesi unggah kedaluwarsa, unggahan dihentikan');
                    destroyJob(job);
                    return;
                }
                attempt += 1;
                if (attempt > MAX_RETRY) {
                    job.state = 'error';
                    job.meta = 'Koneksi bermasalah — klik Lanjutkan untuk mencoba lagi';
                    paint(job); notify(`Unggahan "${job.name}" terputus`);
                    await new Promise((resolve) => { job.resumeWait = resolve; });
                    job.resumeWait = null;
                    if (job.cancelled) return;
                    job.state = 'uploading';
                    attempt = 0;
                    continue;
                }
                job.meta = `Koneksi bermasalah, mencoba ulang (${attempt}/${MAX_RETRY})…`;
                paint(job);
                await new Promise((resolve) => setTimeout(resolve, Math.min(1000 * attempt, 4000)));
            }
        }

        job.received.add(index);
        saveReg();
        paintProgress(job);
    }

    await completeJob(job, true);
}

async function completeJob(job, callComplete) {
    try {
        if (callComplete) await api(`/upload/${job.sessionId}/complete`, { method: 'POST' });
    } catch (error) {
        job.state = 'error'; job.meta = error.message || 'Gagal menyelesaikan unggahan';
        paint(job); notify(job.meta);
        return;
    }
    job.state = 'done';
    job.meta = 'Unggahan selesai';
    paint(job);
    notify(`"${job.name}" berhasil diunggah`);
    setTimeout(() => {
        destroyJob(job);
        // Muat ulang daftar file hanya bila tak ada unggahan lain berjalan
        // dan tak ada unduhan terkelola yang akan terputus oleh reload.
        const downloadActive = document.querySelector('[data-action="dl-cancel"]');
        if (jobs.size === 0 && !downloadActive) window.location.reload();
    }, 900);
}

export function toggleUpload(cid) {
    const job = jobs.get(cid);
    if (!job || job.state === 'done') return;

    if (job.state === 'needs-file') {
        const input = document.createElement('input');
        input.type = 'file';
        input.onchange = async () => {
            const file = input.files?.[0];
            if (!file) return;
            if (file.name !== job.name || file.size !== job.size) {
                return notify('File yang dipilih harus sama persis (nama & ukuran) dengan unggahan awal');
            }
            job.file = file;
            job.fingerprint = await fingerprintOf(file);
            job.state = 'uploading';
            job.meta = 'Melanjutkan unggahan…';
            paint(job);
            try {
                await attachSession(job);
            } catch (error) {
                job.state = 'needs-file'; job.meta = 'Terhenti — file belum dipilih';
                paint(job); notify(error.message || 'Gagal melanjutkan sesi');
                return;
            }
            runJob(job);
        };
        input.click();
        return;
    }

    if (job.state === 'error') { job.resumeWait?.(); return; }
    setPaused(job, !job.paused);
}

export function cancelUpload(cid) {
    const job = jobs.get(cid);
    if (!job) return;
    job.cancelled = true;
    job.paused = false;
    job.resumeWait?.();
    job.controller?.abort();
    if (job.sessionId) api(`/upload/${job.sessionId}`, { method: 'DELETE' }).catch(() => {});
    notify(`Unggahan "${job.name}" dibatalkan`);
    destroyJob(job);
}

/* Pulihkan semua unggahan aktif dari registry (dipanggil di setiap halaman). */
export async function restoreUploads() {
    for (const entry of loadReg()) {
        if (!entry.sessionId || [...jobs.values()].some((job) => job.sessionId === entry.sessionId)) continue;
        let status;
        try {
            status = await api(`/upload/${entry.sessionId}`);
        } catch { continue; }
        if (status.status !== 'uploading') { idbDel(entry.sessionId); continue; }

        const job = {
            cid: entry.cid || newCid(), sessionId: entry.sessionId, file: null,
            fingerprint: entry.fingerprint, name: entry.name, size: entry.size,
            mime: entry.mime, lastModified: entry.lastModified, folderId: entry.folderId,
            paused: !!entry.paused, cancelled: false,
            received: new Set(status.received || []),
            chunkSize: entry.chunkSize || CHUNK_FALLBACK, totalChunks: entry.totalChunks || status.total_chunks || 0,
            state: 'uploading', meta: 'Melanjutkan unggahan…', controller: null,
            speed: { lastTime: 0, lastReceived: 0, ema: 0 },
        };
        jobs.set(job.cid, job);

        const blob = await idbGet(entry.sessionId);
        if (blob && blob.size === job.size) {
            job.file = blob;
            ensureUI(job);
            if (job.paused) setPaused(job, true);
            runJob(job);
        } else {
            job.state = 'needs-file';
            job.meta = 'Terhenti — klik Lanjutkan & pilih file yang sama untuk melanjutkan';
            ensureUI(job);
        }
    }
    saveReg();
    syncCardVisibility();
}

export { restoreUploads as restoreUpload };
