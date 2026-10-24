// Pengelola unduhan dengan progres (seperti halaman download Chrome, tapi di dalam aplikasi).
// File diambil via fetch streaming sehingga progres, kecepatan, jeda & batal bisa ditampilkan
// sebagai baris di tabel halaman Download — sejajar dengan baris file lainnya.
//   - Jeda = berhenti membaca stream (koneksi tetap terbuka, server tertahan oleh TCP),
//     jadi Lanjutkan tidak perlu mengulang dari awal dan tidak butuh header Range.
//   - Di Chrome/Edge, file ditulis langsung ke disk via File System Access API
//     (pengguna memilih lokasi simpan di awal) sehingga file raksasa tidak memenuhi RAM.
//   - Browser lain: potongan ditampung sebagai Blob lalu disimpan otomatis saat selesai.
// Catatan: unduhan dikelola JavaScript halaman ini; berpindah halaman akan menghentikannya
// (berbeda dengan unggahan yang tersimpan di server dan bisa dilanjutkan lintas halaman).

const $ = (selector, root = document) => root.querySelector(selector);
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
const notify = (message) => window.notify?.(message);

const formatBytes = (n) => {
    const units = ['B', 'KB', 'MB', 'GB'];
    let i = 0;
    while (n >= 1024 && i < units.length - 1) {
        n /= 1024;
        i++;
    }
    return `${n.toFixed(i === 0 ? 0 : 1).replace('.', ',')} ${units[i]}`;
};

const formatEta = (seconds) => {
    if (!isFinite(seconds) || seconds <= 0) return '';
    if (seconds < 60) return `${Math.max(1, Math.round(seconds))} dtk`;
    if (seconds < 3600) return `${Math.round(seconds / 60)} menit`;
    return `${Math.floor(seconds / 3600)} jam ${Math.round((seconds % 3600) / 60)} menit`;
};

const downloads = new Map(); // fileId -> state
const queue = [];
let queueRunning = false;

function rowAnchor() {
    // Gunakan baris unggahan aktif terakhir sebagai anchor bila ada,
    // agar baris unduhan baru tampil di bawah unggahan.
    const uploadRows = [...document.querySelectorAll('.ju-row')];
    if (uploadRows.length > 0) return uploadRows[uploadRows.length - 1];
    // Jika tidak ada upload aktif, insert setelah template/header.
    return $('#upload-row-template') || $('#dl-table-head');
}

function buildRow(dl) {
    const anchor = rowAnchor();
    if (!anchor) return null;

    const row = document.createElement('div');
    row.dataset.dlRow = dl.id;
    row.className = 'grid grid-cols-[36px_minmax(260px,1.8fr)_1fr_110px_150px] items-center gap-4 border-b border-sky-100 bg-sky-50/60 px-6 py-3.5';
    row.innerHTML = `
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" /></svg>
        </div>
        <div class="min-w-0">
            <p class="truncate text-sm font-bold text-slate-900"></p>
            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-sky-100">
                <div class="dl-bar h-full rounded-full bg-sky-500 transition-all" style="width: 0%"></div>
            </div>
            <p class="dl-meta mt-1 text-xs text-slate-500">Menyiapkan unduhan…</p>
        </div>
        <span class="dl-status text-sm font-semibold text-sky-700">0%</span>
        <span class="text-sm font-semibold text-slate-700">${formatBytes(dl.size)}</span>
        <div class="flex items-center justify-end gap-2">
            <button type="button" data-action="dl-toggle" data-id="${dl.id}" class="flex h-9 items-center rounded-xl bg-sky-600 px-3.5 text-xs font-bold text-white shadow-md shadow-sky-600/20 transition hover:bg-sky-500">
                <span class="dl-toggle-label">Jeda</span>
            </button>
            <button type="button" data-action="dl-cancel" data-id="${dl.id}" class="flex h-9 items-center rounded-xl border border-rose-200 px-3.5 text-xs font-bold text-rose-600 transition hover:bg-rose-50">
                Batal
            </button>
        </div>`;
    row.querySelector('p.truncate').textContent = dl.name;
    anchor.after(row);
    dl.row = row;
    return row;
}

function paint(dl) {
    if (!dl.row) return;
    const percent = dl.size > 0 ? (dl.received / dl.size) * 100 : 0;
    dl.row.querySelector('.dl-bar').style.width = `${percent}%`;
    const status = dl.row.querySelector('.dl-status');
    if (dl.status === 'downloading') status.textContent = `${Math.round(percent)}%`;
    const meta = dl.row.querySelector('.dl-meta');
    if (dl.status === 'downloading') {
        meta.textContent = dl.speed > 0
            ? `${formatBytes(dl.received)} dari ${formatBytes(dl.size)} • ${formatBytes(dl.speed)}/dtk • tersisa ${formatEta((dl.size - dl.received) / dl.speed)}`
            : `${formatBytes(dl.received)} dari ${formatBytes(dl.size)}`;
    }
}

function setState(dl, status, metaText) {
    dl.status = status;
    if (!dl.row) return;
    const statusEl = dl.row.querySelector('.dl-status');
    const meta = dl.row.querySelector('.dl-meta');
    const label = dl.row.querySelector('.dl-toggle-label');
    if (status === 'paused') {
        statusEl.textContent = 'Dijeda';
        if (label) label.textContent = 'Lanjutkan';
        meta.textContent = metaText || `${formatBytes(dl.received)} dari ${formatBytes(dl.size)} • dijeda`;
    } else if (status === 'downloading') {
        if (label) label.textContent = 'Jeda';
    } else if (status === 'done') {
        statusEl.textContent = 'Selesai';
        meta.textContent = metaText || 'File tersimpan di perangkat Anda';
    } else if (status === 'error') {
        statusEl.textContent = 'Gagal';
        meta.textContent = metaText || 'Unduhan gagal';
    }
}

async function runDownload(dl) {
    let writable = null;
    let parts = null;

    try {
        // Mode tulis langsung ke disk (Chrome/Edge) — hanya dari klik langsung, bukan antrean.
        if (!dl.fromQueue && window.showSaveFilePicker) {
            try {
                const handle = await window.showSaveFilePicker({ suggestedName: dl.name });
                writable = await handle.createWritable();
            } catch {
                return cleanup(dl, false); // pengguna membatalkan dialog simpan
            }
        } else {
            parts = [];
        }

        const response = await fetch(dl.url, { credentials: 'same-origin', signal: dl.controller.signal });
        if (!response.ok || !response.body) throw new Error(`Unduhan gagal (HTTP ${response.status})`);
        if (dl.size === 0) dl.size = Number(response.headers.get('Content-Length')) || 0;

        const reader = response.body.getReader();
        dl.reader = reader;
        setState(dl, 'downloading');
        paint(dl);

        while (true) {
            while (dl.paused && !dl.cancelled) await sleep(250);
            if (dl.cancelled) {
                await reader.cancel().catch(() => {});
                break;
            }
            const t0 = performance.now();
            const { done, value } = await reader.read();
            if (done) break;
            if (writable) await writable.write(value);
            else parts.push(value);
            dl.received += value.byteLength;
            const current = value.byteLength / Math.max(0.001, (performance.now() - t0) / 1000);
            dl.speed = dl.speed ? dl.speed * 0.7 + current * 0.3 : current;
            paint(dl);
        }

        if (dl.cancelled) {
            if (writable) await writable.abort().catch(() => {});
            return cleanup(dl, false);
        }

        if (writable) {
            await writable.close();
        } else {
            const blob = new Blob(parts, { type: dl.mime || 'application/octet-stream' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = dl.name;
            document.body.appendChild(a);
            a.click();
            setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 4000);
        }

        setState(dl, 'done');
        notify(`Unduhan selesai: ${dl.name}`);
        setTimeout(() => cleanup(dl, true), 6000);
    } catch (error) {
        if (dl.cancelled) return cleanup(dl, false);
        if (writable) await writable.abort().catch(() => {});
        setState(dl, 'error', error.message);
        notify(error.message);
        setTimeout(() => cleanup(dl, true), 8000);
    }
}

function cleanup(dl, keepRow) {
    downloads.delete(dl.id);
    if (!keepRow && dl.row) dl.row.remove();
    if (keepRow && dl.row) dl.row.remove();
    dl.row = null;
    advanceQueue();
}

function start(item, { fromQueue = false } = {}) {
    if (downloads.has(item.id)) {
        notify('File ini sedang diunduh');
        return;
    }
    const dl = {
        id: item.id,
        name: item.name,
        size: Number(item.size) || 0,
        mime: item.mime || '',
        url: item.url,
        received: 0,
        speed: 0,
        paused: false,
        cancelled: false,
        status: 'downloading',
        fromQueue,
        controller: new AbortController(),
        reader: null,
        row: null,
    };
    downloads.set(dl.id, dl);
    buildRow(dl);
    runDownload(dl);
}

function enqueue(items) {
    items.forEach((item) => queue.push(item));
    advanceQueue();
}

async function advanceQueue() {
    if (queueRunning) return;
    const next = queue.shift();
    if (!next) return;
    queueRunning = true;
    start(next, { fromQueue: true });
    // Antrean berjalan satu per satu: tunggu sampai unduhan ini selesai/gagal/batal.
    const check = setInterval(() => {
        if (!downloads.has(next.id)) {
            clearInterval(check);
            queueRunning = false;
            advanceQueue();
        }
    }, 600);
}

function toggle(id) {
    const dl = downloads.get(Number(id));
    if (!dl || dl.status === 'done' || dl.status === 'error') return;
    dl.paused = !dl.paused;
    if (dl.paused) setState(dl, 'paused');
    else {
        setState(dl, 'downloading');
        paint(dl);
    }
}

function cancel(id) {
    const dl = downloads.get(Number(id));
    if (!dl) return;
    dl.cancelled = true;
    dl.paused = false;
    dl.controller.abort();
    notify(`Unduhan dibatalkan: ${dl.name}`);
    // runDownload akan membersihkan baris & file sebagian.
    setTimeout(() => { if (downloads.get(dl.id) === dl) cleanup(dl, false); }, 800);
}

window.RuangDownloads = { start, enqueue, toggle, cancel };
export const Downloads = window.RuangDownloads;
