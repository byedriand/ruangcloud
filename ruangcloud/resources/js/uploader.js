// Resumable chunk uploader (KAK F-03 / F-04) — File API + fetch, tanpa library.
//   init -> kirim chunk satu per satu (SHA-256 per chunk) -> complete -> polling status.
// Sesi bisa dijeda, dilanjutkan, dibatalkan, dan otomatis dilanjutkan bila file yang sama dipilih lagi.

const MAX_SIZE = 5 * 1024 ** 3; // 5 GB
const MAX_RETRY = 5;

const $ = (selector) => document.querySelector(selector);
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
const notify = (message) => window.notify?.(message);

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const toHex = (buffer) => [...new Uint8Array(buffer)].map((b) => b.toString(16).padStart(2, '0')).join('');
const sha256 = async (data) => toHex(await crypto.subtle.digest('SHA-256', data));

export async function api(url, { method = 'GET', body, json } = {}) {
    const headers = { Accept: 'application/json', 'X-CSRF-TOKEN': csrf() };
    if (json) {
        headers['Content-Type'] = 'application/json';
        body = JSON.stringify(json);
    }

    const response = await fetch(url, { method, headers, body, credentials: 'same-origin' });
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
        i++;
    }
    return `${n.toFixed(i === 0 ? 0 : 1).replace('.', ',')} ${units[i]}`;
};

const formatEta = (seconds) => {
    if (!isFinite(seconds)) return '';
    if (seconds < 60) return `${Math.max(1, Math.round(seconds))} dtk`;
    if (seconds < 3600) return `${Math.round(seconds / 60)} menit`;
    return `${Math.floor(seconds / 3600)} jam ${Math.round((seconds % 3600) / 60)} menit`;
};

/* ---------- UI kartu progres ---------- */
const ui = {
    show(name) {
        $('#upload-card').hidden = false;
        $('#up-name').textContent = name;
        this.progress(0);
        this.meta('Menyiapkan unggahan…');
        this.paused(false);
    },
    hide() {
        $('#upload-card').hidden = true;
    },
    progress(percent) {
        $('#up-bar').style.width = `${percent}%`;
        $('#up-status').textContent = `${Math.round(percent)}%`;
    },
    status(text) {
        $('#up-status').textContent = text;
    },
    meta(text) {
        $('#up-meta').textContent = text;
    },
    paused(isPaused) {
        $('#icon-pause').hidden = isPaused;
        $('#icon-resume').hidden = !isPaused;
        $('#up-bar').classList.toggle('upload-progress', !isPaused);
        $('#up-toggle').setAttribute('aria-label', isPaused ? 'Lanjutkan unggahan' : 'Jeda unggahan');
    },
};

/* ---------- State ---------- */
let job = null; // { file, session, paused, cancelled, needsRefresh, speed }

const waitIfPaused = async () => {
    while (job.paused && !job.cancelled) await sleep(250);
};

export async function startUpload(file) {
    if (job) return notify('Masih ada unggahan yang berjalan');
    if (!window.crypto?.subtle) return notify('Buka lewat localhost atau HTTPS agar checksum bisa dihitung');
    if (file.size === 0) return notify('File kosong tidak dapat diunggah');
    if (file.size > MAX_SIZE) return notify('Maksimum 5 GB per file');

    job = { file, session: null, paused: false, cancelled: false, needsRefresh: false, speed: 0 };
    ui.show(file.name);

    try {
        const fingerprint = await sha256(new TextEncoder().encode(`${file.name}|${file.size}|${file.lastModified}`));
        job.session = await api('/upload/init', {
            method: 'POST',
            json: {
                name: file.name,
                size: file.size,
                mime: file.type || null,
                fingerprint,
                folder_id: Number(document.body.dataset.folderId) || null,
            },
        });
        if (job.session.resumed) notify('Melanjutkan unggahan sebelumnya');

        await sendChunks();

        if (job.cancelled) {
            await api(`/upload/${job.session.session_id}`, { method: 'DELETE' }).catch(() => {});
            return;
        }
        await finish();
    } catch (error) {
        ui.status('Gagal');
        ui.meta(error.message);
        ui.paused(true);
        notify(error.message);
        const failedJob = job;
        setTimeout(() => {
            if (job === null || job === failedJob) ui.hide();
        }, 9000);
    } finally {
        job = null;
    }
}

async function sendChunks() {
    const { session_id: id, chunk_size: chunkSize, total_chunks: total } = job.session;
    const size = job.file.size;
    const sizeOf = (i) => (i < total - 1 ? chunkSize : size - chunkSize * (total - 1));

    let received = new Set(job.session.received);
    let done = [...received].reduce((sum, i) => sum + sizeOf(i), 0);
    const nextMissing = () => {
        for (let i = 0; i < total; i++) if (!received.has(i)) return i;
        return -1;
    };
    const paint = () => {
        ui.progress((done / size) * 100);
        const eta = job.speed > 0 ? formatEta((size - done) / job.speed) : '';
        ui.meta(
            job.speed > 0
                ? `${formatBytes(job.speed)}/dtk • tersisa ${eta}`
                : `${formatBytes(done)} dari ${formatBytes(size)}`,
        );
    };
    paint();

    let index;
    while ((index = nextMissing()) !== -1) {
        await waitIfPaused();
        if (job.cancelled) return;

        if (job.needsRefresh) {
            // Setelah jeda / koneksi putus: minta daftar chunk terbaru dari server.
            const status = await api(`/upload/${id}`);
            received = new Set(status.received);
            done = [...received].reduce((sum, i) => sum + sizeOf(i), 0);
            job.needsRefresh = false;
            paint();
            continue;
        }

        const start = index * chunkSize;
        const blob = job.file.slice(start, start + sizeOf(index));
        const checksum = await sha256(await blob.arrayBuffer());
        const form = new FormData();
        form.append('index', index);
        form.append('checksum', checksum);
        form.append('chunk', blob, `${index}.part`);

        let attempt = 0;
        const t0 = performance.now();
        while (true) {
            if (job.cancelled) return;
            try {
                await api(`/upload/${id}/chunk`, { method: 'POST', body: form });
                break;
            } catch (error) {
                const retryable = error.status === undefined || error.status >= 500;
                if (!retryable) throw error; // 403/409/410/419/422: hentikan

                attempt++;
                if (attempt > MAX_RETRY) {
                    // Koneksi tidak pulih: jeda otomatis, lanjut dari chunk terakhir saat pengguna menekan lanjutkan.
                    job.paused = true;
                    job.needsRefresh = true;
                    ui.paused(true);
                    ui.status('Dijeda');
                    ui.meta('Koneksi terputus. Tekan lanjutkan untuk meneruskan.');
                    await waitIfPaused();
                    break;
                }
                ui.meta(`Koneksi bermasalah, mencoba ulang (${attempt}/${MAX_RETRY})…`);
                await sleep(Math.min(8000, 800 * 2 ** attempt));
            }
        }
        if (job.needsRefresh) continue; // chunk ini belum terkirim; sinkron ulang lalu lanjut

        received.add(index);
        done += blob.size;
        const current = blob.size / Math.max(0.001, (performance.now() - t0) / 1000);
        job.speed = job.speed ? job.speed * 0.7 + current * 0.3 : current;
        paint();
    }
}

async function finish() {
    const { session_id: id } = job.session;
    ui.progress(100);
    ui.meta('Menggabungkan file di server…');
    const completeRes = await api(`/upload/${id}/complete`, { method: 'POST' });
    if (completeRes?.status === 'completed') {
        ui.status('Selesai');
        ui.meta('File tersimpan dan sedang direplikasi ke node lain');
        ui.paused(true);
        notify('File berhasil diunggah');
        setTimeout(() => window.location.reload(), 1200);
        return;
    }

    for (let n = 0; n < 120; n++) {
        await sleep(1500);
        if (job.cancelled) return;
        const status = await api(`/upload/${id}`);
        if (status.status === 'completed') {
            ui.status('Selesai');
            ui.meta('File tersimpan dan sedang direplikasi ke node lain');
            ui.paused(true);
            notify('File berhasil diunggah');
            setTimeout(() => window.location.reload(), 1200);
            return;
        }
        if (status.status === 'failed') throw new Error(status.error || 'Gagal menggabungkan file');
    }
    throw new Error('Server belum selesai memproses penggabungan berkas. Silakan coba lagi.');
}

export function toggleUpload() {
    if (!job) return;
    job.paused = !job.paused;
    if (!job.paused) job.needsRefresh = true;
    ui.paused(job.paused);
    ui.status(job.paused ? 'Dijeda' : `${Math.round(parseFloat($('#up-bar').style.width) || 0)}%`);
    ui.meta(job.paused ? 'Unggahan dijeda' : 'Melanjutkan…');
}

export function cancelUpload() {
    if (!job) return ui.hide();
    job.cancelled = true;
    job.paused = false;
    ui.hide();
    notify('Unggahan dibatalkan');
}
