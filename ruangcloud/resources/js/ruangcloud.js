// Interaksi RuangCloud (versi vanilla JS dari state React di prototipe Figma).
// Semua elemen interaktif memakai atribut data-action / id, tanpa library tambahan.

import { api, startUpload, toggleUpload, cancelUpload, restoreUploads } from './uploader';
import { Downloads } from './downloader';

const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];

/* ---------- Toast ---------- */
let toastTimer;
function notify(message, duration = 2400) {
    const toast = $('#toast');
    if (!toast) return;
    toast.textContent = message;
    toast.hidden = false;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => (toast.hidden = true), duration);
}

window.notify = notify;

/* ---------- Popup khusus bertema RuangCloud ---------- */
let dialogResolve = null;

function ensureDialog() {
    if ($('#rc-dialog')) return;
    const wrap = document.createElement('div');
    wrap.id = 'rc-dialog';
    wrap.hidden = true;
    wrap.className = 'fixed inset-0 z-[90] flex items-center justify-center bg-[#052e22]/55 p-4 backdrop-blur-sm';
    wrap.innerHTML = `
      <div data-dialog-card class="w-full max-w-sm scale-95 rounded-[26px] glass-card p-6 opacity-0 shadow-2xl transition-all duration-200">
        <div class="flex flex-col items-center text-center">
          <div data-dialog-icon class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl text-white shadow-lg"></div>
          <h3 data-dialog-title class="text-lg font-extrabold tracking-tight text-slate-900"></h3>
          <p data-dialog-message class="mt-1.5 text-sm leading-relaxed text-slate-600"></p>
        </div>
        <div data-dialog-input-wrap class="mt-5" hidden>
          <label data-dialog-label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500"></label>
          <input data-dialog-input type="text" class="w-full rounded-xl border border-white/60 bg-white/70 px-3.5 py-2.5 text-sm font-semibold text-slate-800 shadow-inner outline-none backdrop-blur transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/15" />
        </div>
        <div class="mt-6 flex gap-3">
          <button type="button" data-dialog-cancel class="h-11 flex-1 rounded-xl border border-white/60 bg-white/60 text-sm font-bold text-slate-600 shadow-sm backdrop-blur transition hover:bg-white/85">Batal</button>
          <button type="button" data-dialog-ok class="h-11 flex-1 rounded-xl text-sm font-bold text-white shadow-lg transition active:scale-[0.98]"></button>
        </div>
      </div>`;
    document.body.appendChild(wrap);

    const close = (value) => {
        $('[data-dialog-card]', wrap).classList.add('scale-95', 'opacity-0');
        setTimeout(() => { wrap.hidden = true; document.body.classList.remove('overflow-hidden'); }, 150);
        if (dialogResolve) { dialogResolve(value); dialogResolve = null; }
    };
    wrap._close = close;

    $('[data-dialog-cancel]', wrap).addEventListener('click', () => close(null));
    wrap.addEventListener('click', (e) => { if (e.target === wrap) close(null); });
    document.addEventListener('keydown', (e) => {
        if (wrap.hidden) return;
        if (e.key === 'Escape') close(null);
        if (e.key === 'Enter' && document.activeElement === $('[data-dialog-input]', wrap)) {
            e.preventDefault();
            $('[data-dialog-ok]', wrap).click();
        }
    });
}

const ICON_INFO = '<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
const ICON_TRASH = '<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
const ICON_PENCIL = '<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';

function openDialog({ title, message, label = null, value = '', okText = 'Oke', danger = false }) {
    ensureDialog();
    const wrap = $('#rc-dialog');
    const icon = $('[data-dialog-icon]', wrap);
    icon.className = 'mb-4 flex h-14 w-14 items-center justify-center rounded-2xl text-white shadow-lg ' + (danger
        ? 'bg-gradient-to-br from-rose-400 to-red-500 shadow-rose-500/30'
        : 'bg-gradient-to-br from-emerald-400 to-teal-500 shadow-emerald-500/30');
    icon.innerHTML = danger ? ICON_TRASH : (label !== null ? ICON_PENCIL : ICON_INFO);
    $('[data-dialog-title]', wrap).textContent = title;
    $('[data-dialog-message]', wrap).textContent = message;
    const inputWrap = $('[data-dialog-input-wrap]', wrap);
    const input = $('[data-dialog-input]', wrap);
    inputWrap.hidden = label === null;
    if (label !== null) {
        $('[data-dialog-label]', wrap).textContent = label;
        input.value = value;
    }
    const ok = $('[data-dialog-ok]', wrap);
    ok.textContent = okText;
    ok.className = 'h-11 flex-1 rounded-xl text-sm font-bold text-white shadow-lg transition active:scale-[0.98] ' + (danger
        ? 'bg-gradient-to-r from-rose-500 to-red-500 shadow-rose-600/25 hover:from-rose-400 hover:to-red-400'
        : 'bg-gradient-to-r from-emerald-500 to-teal-500 shadow-emerald-600/25 hover:from-emerald-400 hover:to-teal-400');
    ok.onclick = () => wrap._close(label === null ? true : input.value);

    wrap.hidden = false;
    document.body.classList.add('overflow-hidden');
    requestAnimationFrame(() => $('[data-dialog-card]', wrap).classList.remove('scale-95', 'opacity-0'));
    if (label !== null) setTimeout(() => { input.focus(); input.select(); }, 60);

    return new Promise((resolve) => { dialogResolve = resolve; });
}

const confirmDialog = (opts) => openDialog(opts).then((v) => v === true);
const promptDialog = (opts) => openDialog(opts);


/* ---------- Sidebar (mobile) ---------- */
function setSidebar(open) {
    const sidebar = $('#sidebar');
    const backdrop = $('#sidebar-backdrop');
    if (!sidebar) return;
    sidebar.classList.toggle('translate-x-0', open);
    sidebar.classList.toggle('-translate-x-full', !open);
    if (backdrop) backdrop.hidden = !open;
}

/* ---------- Navigasi sidebar ---------- */
function setNav(label) {
    const list = $('[data-nav-list]');
    if (!list) return;

    const active = list.dataset.activeClass.split(' ');
    const inactive = list.dataset.inactiveClass.split(' ');
    $$('[data-nav]', list).forEach((button) => {
        const on = button.dataset.nav === label;
        button.classList.remove(...(on ? inactive : active));
        button.classList.add(...(on ? active : inactive));
    });

    const title = $('#page-title');
    if (title) {
        const isHome = title.dataset.homeLabel && label === title.dataset.homeLabel;
        title.textContent = isHome ? title.dataset.homeText : label;
    }

    const subtitle = $('#page-subtitle');
    if (subtitle) {
        const isHome = label === title?.dataset.homeLabel;
        subtitle.textContent = isHome
            ? subtitle.dataset.homeText
            : subtitle.dataset.template.replace(':label', label.toLowerCase());
    }
}

/* ---------- Daftar file: pencarian + list/grid ---------- */
let fileView = 'list';

function renderFiles() {
    const listView = $('#view-list');
    const gridView = $('#view-grid');
    if (!listView || !gridView) return;

    const query = ($('#search')?.value ?? '').toLowerCase();
    let visible = 0;
    $$('[data-file-name]').forEach((item) => {
        const match = item.dataset.fileName.includes(query);
        item.hidden = !match;
        if (match && item.closest('#view-list')) visible++;
    });

    const count = $('#file-count');
    if (count) count.textContent = `${visible} file ditampilkan`;

    $('#file-empty').hidden = visible > 0;
    listView.hidden = visible === 0 || fileView !== 'list';
    gridView.hidden = visible === 0 || fileView !== 'grid';

    $$('[data-view-btn]').forEach((button) => {
        const on = button.dataset.viewBtn === fileView;
        button.classList.toggle('bg-white', on);
        button.classList.toggle('text-[#0aa982]', on);
        button.classList.toggle('shadow-sm', on);
        button.classList.toggle('text-[#85908c]', !on);
    });
}

/* ---------- Modal upload & kartu progres ---------- */
function setUploadModal(open) {
    const modal = $('#upload-modal');
    if (modal) modal.hidden = !open;
}

/* ---------- Pratinjau file ---------- */
const PREVIEWABLE_IMAGE = ['image/jpeg','image/png','image/gif','image/webp','image/svg+xml','image/bmp'];
const PREVIEWABLE_VIDEO = ['video/mp4','video/webm','video/ogg','video/quicktime'];
const PREVIEWABLE_AUDIO = ['audio/mpeg','audio/ogg','audio/wav','audio/flac','audio/aac','audio/mp4'];
const PREVIEWABLE_TEXT  = ['text/plain','text/csv','text/html','text/css','application/json','application/xml','text/xml'];

function previewTypeOf(mime, name) {
    const ext = (name.split('.').pop() || '').toLowerCase();
    if (PREVIEWABLE_IMAGE.includes(mime) || ['jpg','jpeg','png','gif','webp','svg','bmp'].includes(ext)) return 'image';
    if (mime === 'application/pdf' || ext === 'pdf') return 'pdf';
    if (PREVIEWABLE_VIDEO.includes(mime) || ['mp4','webm','ogv','mov'].includes(ext)) return 'video';
    if (PREVIEWABLE_AUDIO.includes(mime) || ['mp3','ogg','wav','flac','aac','m4a'].includes(ext)) return 'audio';
    if (PREVIEWABLE_TEXT.includes(mime) || ['txt','csv','json','md','xml','log','js','ts','css','html','php','py'].includes(ext)) return 'text';
    return 'unsupported';
}

function hideAllPreviewSlots() {
    ['preview-img','preview-pdf','preview-video','preview-audio-wrap','preview-text','preview-unsupported','preview-loading']
        .forEach(id => { const el = $(`#${id}`); if (el) el.hidden = true; });
}

let previewFile = null;
async function openPreview(fileId, name, mime, previewUrl, downloadUrl, updated = '') {
    previewFile = { id: Number(fileId), name, mime, size: 0, url: downloadUrl };
    const modal = $('#preview-modal');
    if (!modal) return;

    // Header
    $('#preview-name').textContent = name;
    $('#preview-meta').textContent  = `${mime || 'File'}${updated ? ' • Terakhir diubah: ' + updated : ''}`;
    $('#preview-download').href     = downloadUrl;
    $('#preview-unsupported-dl') && ($('#preview-unsupported-dl').href = downloadUrl);

    // Reset
    hideAllPreviewSlots();
    $('#preview-loading').hidden = false;
    modal.hidden = false;
    document.body.style.overflow = 'hidden';

    const type = previewTypeOf(mime, name);

    if (type === 'image') {
        const img = $('#preview-img');
        img.onload  = () => { $('#preview-loading').hidden = true; img.hidden = false; };
        img.onerror = () => { $('#preview-loading').hidden = true; showUnsupported(downloadUrl); };
        img.src = previewUrl;
    } else if (type === 'pdf') {
        $('#preview-loading').hidden = true;
        const frame = $('#preview-pdf');
        frame.src    = previewUrl;
        frame.hidden = false;
    } else if (type === 'video') {
        $('#preview-loading').hidden = true;
        const vid = $('#preview-video');
        vid.src    = previewUrl;
        vid.hidden = false;
    } else if (type === 'audio') {
        $('#preview-loading').hidden = true;
        const aud = $('#preview-audio');
        aud.src = previewUrl;
        $('#preview-audio-wrap').hidden = false;
    } else if (type === 'text') {
        try {
            const res  = await fetch(previewUrl, { credentials: 'same-origin' });
            const text = await res.text();
            const pre  = $('#preview-text');
            pre.textContent = text.slice(0, 50000) + (text.length > 50000 ? '\n\n… (konten terpotong)' : '');
            $('#preview-loading').hidden = true;
            pre.hidden = false;
        } catch {
            showUnsupported(downloadUrl);
        }
    } else {
        $('#preview-loading').hidden = true;
        showUnsupported(downloadUrl);
    }
}

// Unduh dari pratinjau ikut jalur terpantau (progres + catatan selesai).
['preview-download', 'preview-unsupported-dl'].forEach((id) => {
    document.getElementById(id)?.addEventListener('click', (event) => {
        if (!previewFile) return;
        event.preventDefault();
        beginDownload({ ...previewFile });
        closePreview();
    });
});

function showUnsupported(downloadUrl) {
    const el = $('#preview-unsupported');
    if (!el) return;
    $('#preview-unsupported-dl') && ($('#preview-unsupported-dl').href = downloadUrl);
    el.hidden = false;
}

function closePreview() {
    const modal = $('#preview-modal');
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = '';
    // Stop media
    const vid = $('#preview-video'); if (vid) { vid.pause(); vid.src = ''; }
    const aud = $('#preview-audio'); if (aud) { aud.pause(); aud.src = ''; }
    const pdf = $('#preview-pdf');   if (pdf) pdf.src = '';
    const img = $('#preview-img');   if (img) img.src = '';
}


/* ---------- Aksi file / folder / berbagi / sampah ---------- */
// Jalankan permintaan, tampilkan toast, lalu muat ulang halaman (atau pindah ke redirect).
async function run(request, message, redirect = null) {
    try {
        await request();
        notify(message);
        setTimeout(() => (redirect ? (window.location.href = redirect) : window.location.reload()), 500);
    } catch (error) {
        notify(error.message);
    }
}

async function addFolder(parentId) {
    const name = await promptDialog({ title: 'Folder baru', message: 'Buat folder untuk mengorganisasi file Anda.', label: 'Nama folder baru', okText: 'Buat folder' });
    if (!name || !name.trim()) return;
    run(
        () => api('/folders', { method: 'POST', json: { name: name.trim(), parent_id: parentId ? Number(parentId) : null } }),
        'Folder baru berhasil dibuat',
    );
}

async function renameFolder(id, current) {
    const name = await promptDialog({ title: 'Ubah nama folder', message: 'Nama baru tampil di semua tempat folder ini muncul.', label: 'Nama folder', value: current, okText: 'Simpan' });
    if (!name || !name.trim() || name.trim() === current) return;
    run(() => api(`/folders/${id}`, { method: 'PATCH', json: { name: name.trim() } }), 'Nama folder diubah');
}

async function deleteFolder(id, redirect) {
    if (!await confirmDialog({ title: 'Hapus folder ini?', message: 'Folder hanya bisa dihapus bila kosong.', okText: 'Hapus', danger: true })) return;
    run(() => api(`/folders/${id}`, { method: 'DELETE' }), 'Folder dihapus', redirect);
}

let renameFileState = null;
async function renameFile(id, name, baseName = '', extension = '', type = '') {
    const ext = extension || (name && name.includes('.') ? name.split('.').pop() : '');
    const base = baseName || (ext && name && name.toLowerCase().endsWith('.' + ext.toLowerCase()) ? name.slice(0, -(ext.length + 1)) : name);

    renameFileState = { id, ext, name };

    const modal = $('#rename-modal');
    if (!modal) {
        let input = await promptDialog({ title: 'Ubah nama file', message: 'Ekstensi file ditambahkan otomatis bila belum ada.', label: `Nama file baru (${type || 'file'})`, value: base || name, okText: 'Simpan' });
        if (!input || !input.trim()) return;
        input = input.trim();
        if (ext && !input.toLowerCase().endsWith('.' + ext.toLowerCase())) {
            input = `${input.replace(/\.+$/, '')}.${ext}`;
        }
        run(() => api(`/files/${id}`, { method: 'PATCH', json: { name: input } }), 'Nama file diubah');
        return;
    }

    const typeEl = $('#rename-file-type');
    if (typeEl) typeEl.textContent = type || (ext ? `File ${ext.toUpperCase()}` : 'File');

    const extBadge = $('#rename-file-ext');
    if (extBadge) {
        extBadge.textContent = ext ? `.${ext}` : '';
        extBadge.hidden = !ext;
    }

    const exampleEl = $('#rename-file-example');
    if (exampleEl) exampleEl.textContent = ext ? `cup.${ext}` : 'cup';

    const inputEl = $('#rename-file-input');
    if (inputEl) {
        inputEl.value = base || name;
    }

    modal.hidden = false;
    setTimeout(() => {
        if (inputEl) {
            inputEl.focus();
            inputEl.select();
        }
    }, 50);
}

function confirmRenameFile() {
    if (!renameFileState) return;
    const inputEl = $('#rename-file-input');
    let newName = (inputEl ? inputEl.value : '').trim();
    if (!newName) {
        notify('Nama file tidak boleh kosong');
        return;
    }

    const { id, ext } = renameFileState;
    if (ext && !newName.toLowerCase().endsWith('.' + ext.toLowerCase())) {
        newName = `${newName.replace(/\.+$/, '')}.${ext}`;
    }

    const modal = $('#rename-modal');
    if (modal) modal.hidden = true;
    run(() => api(`/files/${id}`, { method: 'PATCH', json: { name: newName } }), 'Nama file diubah');
}
window.confirmRenameFile = confirmRenameFile;


async function trashFile(id) {
    if (!await confirmDialog({ title: 'Pindahkan ke sampah?', message: 'File ini dipindahkan ke tempat sampah dan masih bisa dipulihkan.', okText: 'Pindahkan' })) return;
    run(() => api(`/files/${id}`, { method: 'DELETE' }), 'File dipindahkan ke sampah');
}

let moveTarget = { type: 'file', id: null };
function openMove(id) {
    moveTarget = { type: 'file', id };
    $('#move-folder').value = '';
    $('#move-modal h2').textContent = 'Pindahkan file';
    $('#move-modal').hidden = false;
}
function openMoveFolder(id) {
    moveTarget = { type: 'folder', id };
    $('#move-folder').value = '';
    $('#move-modal h2').textContent = 'Pindahkan folder';
    $('#move-modal').hidden = false;
}
function confirmMove() {
    const folderId = $('#move-folder').value;
    $('#move-modal').hidden = true;
    if (moveTarget.type === 'folder') {
        run(
            () => api(`/folders/${moveTarget.id}/move`, { method: 'POST', json: { parent_id: folderId ? Number(folderId) : null } }),
            'Folder dipindahkan',
        );
    } else {
        run(
            () => api(`/files/${moveTarget.id}/move`, { method: 'POST', json: { folder_id: folderId ? Number(folderId) : null } }),
            'File dipindahkan',
        );
    }
}

let shareTarget = { type: 'file', id: null };
function openShare(id, name) {
    shareTarget = { type: 'file', id };
    $('#share-file-name').textContent = name;
    $('#share-password').value = '';
    $('#share-form').hidden = false;
    $('#share-result').hidden = true;
    $('#share-modal').hidden = false;
}
function openShareFolder(id, name) {
    shareTarget = { type: 'folder', id };
    $('#share-file-name').textContent = name;
    $('#share-password').value = '';
    $('#share-form').hidden = false;
    $('#share-result').hidden = true;
    $('#share-modal').hidden = false;
}
async function createShare() {
    const password = $('#share-password').value.trim();
    const expiry = $('#share-expiry').value;
    try {
        const data = await api(shareTarget.type === 'folder' ? `/folders/${shareTarget.id}/share` : `/files/${shareTarget.id}/share`, {
            method: 'POST',
            json: { password: password || null, expires_in: expiry ? Number(expiry) : null },
        });
        $('#share-url').value = data.url;
        $('#share-form').hidden = true;
        $('#share-result').hidden = false;
    } catch (error) {
        notify(error.message);
    }
}

async function copyText(text) {
    try {
        await navigator.clipboard.writeText(text);
        notify('Tautan disalin');
    } catch {
        await promptDialog({ title: 'Salin tautan', message: 'Salin tautan di bawah ini secara manual.', label: 'Tautan berbagi', value: text, okText: 'Selesai' });
    }
}

// Klik Unduh dari halaman mana pun: unduhan dijalankan di halaman Download
// agar progresnya langsung terlihat di sana.
function beginDownload(item) {
    if (document.getElementById('dl-history')) {
        Downloads.start(item);
        return;
    }
    try { sessionStorage.setItem('rc-pending-download', JSON.stringify(item)); } catch { /* abaikan */ }
    window.location.href = '/downloads';
}

// Setibanya di halaman Download, jalankan unduhan yang tadi diklik.
(() => {
    if (!document.getElementById('dl-history')) return;
    let raw = null;
    try {
        raw = sessionStorage.getItem('rc-pending-download');
        if (raw) sessionStorage.removeItem('rc-pending-download');
    } catch { /* abaikan */ }
    if (!raw) return;
    try {
        const item = JSON.parse(raw);
        setTimeout(() => Downloads.start(item), 350);
    } catch { /* abaikan */ }
})();

async function deleteDownloadRecord(id) {
    if (!await confirmDialog({ title: 'Hapus dari riwayat unduhan?', message: 'Catatan unduhan ini dihapus dari daftar. Setelah itu file bisa diunduh lagi.', okText: 'Hapus', danger: true })) return;
    run(() => api(`/downloads/${id}`, { method: 'DELETE' }), 'Riwayat unduhan dihapus');
}

async function revokeShare(id) {
    if (!await confirmDialog({ title: 'Cabut tautan berbagi?', message: 'Orang yang memegang tautan tidak bisa lagi mengunduh file ini.', okText: 'Cabut akses', danger: true })) return;
    run(() => api(`/shares/${id}`, { method: 'DELETE' }), 'Tautan dicabut');
}

async function deleteShare(id) {
    if (!await confirmDialog({ title: 'Hapus tautan dari daftar?', message: 'Tautan tidak akan bisa dibuka lagi dan catatan ini hilang permanen.', okText: 'Hapus', danger: true })) return;
    run(() => api(`/shares/${id}/purge`, { method: 'DELETE' }), 'Tautan berbagi dihapus');
}

function restoreFile(id) {
    run(() => api(`/trash/${id}/restore`, { method: 'POST' }), 'File dipulihkan');
}

async function purgeFile(id) {
    if (!await confirmDialog({ title: 'Hapus file permanen?', message: 'File ini dihapus selamanya dan tidak bisa dikembalikan.', okText: 'Hapus permanen', danger: true })) return;
    run(() => api(`/trash/${id}`, { method: 'DELETE' }), 'File dihapus permanen');
}

async function emptyTrash() {
    if (!await confirmDialog({ title: 'Kosongkan sampah?', message: 'Semua file di dalam sampah akan dihapus permanen.', okText: 'Kosongkan', danger: true })) return;
    run(() => api('/trash', { method: 'DELETE' }), 'Sampah dikosongkan');
}

/* ---------- Menu titik tiga pada file ---------- */
const closeMenus = () => $$('[data-menu-panel]').forEach((panel) => (panel.hidden = true));

function toggleMenu(button) {
    // Cari panel — bisa sudah dipindah ke body (setelah portal pertama kali)
    let panel = button._menuPanel;
    if (!panel) {
        const wrap = button.closest('.menu');
        panel = wrap ? $('[data-menu-panel]', wrap) : null;
    }
    if (!panel) return;

    const willOpen = panel.hidden;
    closeMenus();
    if (!willOpen) return;

    // Portal: pindahkan panel ke body agar terbebas dari overflow/stacking context
    if (panel.parentElement !== document.body) {
        document.body.appendChild(panel);
        button._menuPanel = panel;
    }

    // Tampilkan tapi tak terlihat dulu agar browser hitung offsetHeight
    panel.style.visibility = 'hidden';
    panel.hidden = false;

    requestAnimationFrame(() => {
        const r = button.getBoundingClientRect();
        const w = 224;
        const h = panel.offsetHeight;
        panel.style.width  = w + 'px';
        panel.style.left   = Math.max(8, Math.min(r.right - w, window.innerWidth - w - 8)) + 'px';
        const below = r.bottom + 4;
        panel.style.top    = (below + h > window.innerHeight - 8 ? Math.max(8, r.top - h - 4) : below) + 'px';
        panel.style.visibility = '';
    });
}

document.addEventListener('click', (event) => {
    const menuButton = event.target.closest('[data-menu-btn]');
    if (menuButton) { toggleMenu(menuButton); return; }
    if (!event.target.closest('[data-menu-panel]')) closeMenus();
});
window.addEventListener('resize', closeMenus);

/* ---------- Event delegation ---------- */
document.addEventListener('click', (event) => {
    const target = event.target.closest('[data-action]');
    if (!target) return;
    const { action, message, nav } = target.dataset;

    switch (action) {
        case 'open-sidebar':  setSidebar(true); break;
        case 'close-sidebar': setSidebar(false); break;
        case 'open-upload':   setUploadModal(true); break;
        case 'close-upload':  setUploadModal(false); break;
        case 'toast':         notify(message); break;
        case 'nav':
            setNav(nav);
            setSidebar(false);
            if (message) notify(message);
            break;
        case 'set-view':      fileView = target.dataset.view; renderFiles(); break;
        case 'add-folder':    addFolder(target.dataset.parentId); break;
        case 'toggle-upload': toggleUpload(target.dataset.cid); break;
        case 'download-file':
            beginDownload({
                id: Number(target.dataset.fileId),
                name: target.dataset.name,
                size: Number(target.dataset.size || 0),
                mime: target.dataset.mime || '',
                url: target.dataset.url,
            });
            break;
        case 'dl-toggle': Downloads.toggle(target.dataset.id); break;
        case 'dl-cancel': Downloads.cancel(target.dataset.id); break;
        case 'delete-download-record': deleteDownloadRecord(target.dataset.recordId); break;
        case 'cancel-upload': cancelUpload(target.dataset.cid); break;
        case 'delete-file':   trashFile(target.dataset.fileId); break;
        case 'rename-file':
            renameFile(
                target.dataset.fileId,
                target.dataset.name,
                target.dataset.baseName,
                target.dataset.extension,
                target.dataset.type
            );
            break;
        case 'confirm-rename': confirmRenameFile(); break;
        case 'rename-folder': renameFolder(target.dataset.folderId, target.dataset.name); break;
        case 'delete-folder': deleteFolder(target.dataset.folderId, target.dataset.redirect); break;
        case 'move-file':     openMove(target.dataset.fileId); break;
        case 'move-folder':   openMoveFolder(target.dataset.folderId); break;
        case 'confirm-move':  confirmMove(); break;
        case 'share-file':    openShare(target.dataset.fileId, target.dataset.name); break;
        case 'share-folder':  openShareFolder(target.dataset.folderId, target.dataset.name); break;
        case 'folder-info':   openFolderInfo(target.dataset); break;
        case 'create-share':  createShare(); break;
        case 'copy-share':    copyText($('#share-url').value); break;
        case 'copy-link':     copyText(target.dataset.url); break;
        case 'revoke-share':  revokeShare(target.dataset.shareId); break;
        case 'delete-share':  deleteShare(target.dataset.shareId); break;
        case 'restore-file':  restoreFile(target.dataset.fileId); break;
        case 'purge-file':    purgeFile(target.dataset.fileId); break;
        case 'empty-trash':   emptyTrash(); break;
        case 'close-dialog':  $(`#${target.dataset.dialog}`).hidden = true; break;
        case 'preview-file':
            openPreview(
                target.dataset.fileId,
                target.dataset.name,
                target.dataset.mime,
                target.dataset.previewUrl,
                target.dataset.downloadUrl,
                target.dataset.updated
            );
            break;
        case 'close-preview': closePreview(); break;
        case 'toggle-password': {
            const wrap = target.closest('div');
            const input = (wrap && wrap.querySelector('input')) || $('#password');
            if (!input) break;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            target.textContent = show ? 'Sembunyikan' : 'Lihat';
            break;
        }
        case 'open-logout': {
            const modal = $('#logout-modal');
            if (modal) modal.hidden = false;
            break;
        }
        case 'set-role': setRole(target.dataset.role); break;
    }
});

// Klik di area gelap modal (bukan di kotaknya) menutup modal.
document.addEventListener('mousedown', (event) => {
    if (['upload-modal', 'share-modal', 'move-modal', 'rename-modal', 'logout-modal'].includes(event.target.id)) event.target.hidden = true;
    if (event.target.id === 'preview-modal') closePreview();
});

// Tekan ESC menutup modal pratinjau.
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closePreview();
});

document.addEventListener('input', (event) => {
    if (event.target.id === 'search') renderFiles();
});

document.addEventListener('change', (event) => {
    if (event.target.id === 'upload-input') {
        const files = [...(event.target.files || [])];
        event.target.value = '';
        setUploadModal(false);
        files.forEach((file) => startUpload(file));
    }
});

// Drag & drop ke area unggah.
document.addEventListener('dragover', (event) => {
    if (event.target.closest?.('#drop-zone')) event.preventDefault();
});
document.addEventListener('drop', (event) => {
    if (!event.target.closest?.('#drop-zone')) return;
    event.preventDefault();
    const files = [...(event.dataTransfer?.files || [])];
    setUploadModal(false);
    files.forEach((file) => startUpload(file));
});

/* ---------- Halaman login: pilih peran ---------- */
function setRole(role) {
    const form = $('#login-form');
    if (!form) return;
    const admin = role === 'admin';

    $('#role-input').value = role;
    $('#submit-label').textContent = admin ? 'Administrator' : 'Pengguna';

    $$('[data-role]').forEach((button) => {
        const on = button.dataset.role === role;
        button.classList.toggle('bg-gradient-to-r', on);
        button.classList.toggle('from-emerald-500', on);
        button.classList.toggle('to-teal-400', on);
        button.classList.toggle('text-white', on);
        button.classList.toggle('shadow-lg', on);
        button.classList.toggle('shadow-emerald-500/30', on);
        button.classList.toggle('text-slate-500', !on);
    });
}

renderFiles();

// Pulihkan unggahan yang masih berjalan/terjeda dari halaman sebelumnya (bila ada).
restoreUploads();

// -- Halaman register: meter kekuatan kata sandi, cek kecocokan, spinner --
// Diikat lewat bundle ini (jalur yang sama dengan toggle Lihat yang terbukti
// jalan) karena handler inline pada view register tidak berjalan di semua
// lingkungan. Hanya aktif bila elemen-elemen register ada di halaman.
(function () {
    const pw = document.getElementById('password');
    const barsEl = document.getElementById('strength-bars');
    if (!pw || !barsEl) return;

    const pw2 = document.getElementById('password_confirmation');
    const bars = ['sb1', 'sb2', 'sb3', 'sb4'].map((id) => document.getElementById(id));
    const label = document.getElementById('strength-label');
    const matchLabel = document.getElementById('match-label');
    const colors = ['bg-red-400', 'bg-orange-400', 'bg-amber-400', 'bg-emerald-500'];
    const labels = ['Sangat lemah', 'Lemah', 'Cukup', 'Kuat'];
    const labelColors = ['text-red-500', 'text-orange-500', 'text-amber-600', 'text-emerald-600'];

    function checkMatch() {
        if (!pw2 || !matchLabel) return;
        if (pw2.value.length === 0) { matchLabel.classList.add('hidden'); return; }
        matchLabel.classList.remove('hidden');
        if (pw.value === pw2.value) {
            matchLabel.textContent = 'Kata sandi cocok';
            matchLabel.className = 'mt-1.5 text-xs font-bold text-emerald-600';
        } else {
            matchLabel.textContent = 'Kata sandi tidak cocok';
            matchLabel.className = 'mt-1.5 text-xs font-bold text-red-500';
        }
    }

    function checkStrength() {
        const val = pw.value;
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        bars.forEach((b, i) => {
            if (b) b.className = 'h-1.5 flex-1 rounded-full transition-all ' + (i < score ? colors[score - 1] : 'bg-slate-200');
        });
        if (label) {
            if (val.length === 0) {
                label.textContent = 'Masukkan kata sandi untuk melihat kekuatannya';
                label.className = 'mt-1.5 text-xs text-[#64748b]';
            } else {
                label.textContent = labels[score - 1] || 'Sangat lemah';
                label.className = 'mt-1.5 text-xs font-bold ' + (labelColors[score - 1] || 'text-red-500');
            }
        }
        checkMatch();
    }

    pw.addEventListener('input', checkStrength);
    if (pw2) pw2.addEventListener('input', checkMatch);
    checkStrength();

    const form = document.getElementById('register-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const terms = document.getElementById('terms');
            if (terms && !terms.checked) {
                e.preventDefault();
                terms.closest('label').classList.add('border-red-400', 'bg-red-50/50');
                setTimeout(() => terms.closest('label').classList.remove('border-red-400', 'bg-red-50/50'), 2000);
                return;
            }
            const btnText = document.getElementById('btn-text');
            if (btnText) btnText.classList.add('invisible');
            const chev = document.querySelector('.group svg.h-4');
            if (chev) chev.classList.add('invisible');
            const spinner = document.getElementById('btn-spinner');
            if (spinner) spinner.classList.remove('hidden');
            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn) submitBtn.disabled = true;
        });
    }
})();

/* ---------- Informasi folder ---------- */
function openFolderInfo(d) {
    $('#fi-name').textContent = d.name || '-';
    $('#fi-owner').textContent = d.owner || '-';
    $('#fi-contents').textContent = `${d.files || 0} file, ${d.folders || 0} subfolder`;
    $('#fi-size').textContent = d.size || '-';
    $('#fi-created').textContent = d.created || '-';
    $('#folder-info-modal').hidden = false;
}

/* ---------- Seret & lepas: pindahkan file / folder ke folder lain ---------- */
let dragPayload = null;

document.addEventListener('dragstart', (event) => {
    const fileEl = event.target.closest?.('[data-drag-file]');
    const folderEl = event.target.closest?.('[data-drag-folder]');
    const el = fileEl || folderEl;
    if (!el) return;
    dragPayload = fileEl
        ? { kind: 'file', id: Number(fileEl.dataset.dragFile) }
        : { kind: 'folder', id: Number(folderEl.dataset.dragFolder) };
    event.dataTransfer.effectAllowed = 'move';
    try { event.dataTransfer.setData('text/plain', JSON.stringify(dragPayload)); } catch { /* abaikan */ }
    el.classList.add('rc-dragging');
});

document.addEventListener('dragend', () => {
    dragPayload = null;
    $$('.rc-dragging').forEach((el) => el.classList.remove('rc-dragging'));
    $$('[data-drop-folder]').forEach((el) => el.classList.remove('drop-active'));
});

document.addEventListener('dragover', (event) => {
    if (!dragPayload) return;
    const target = event.target.closest?.('[data-drop-folder]');
    if (!target) return;
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
    $$('[data-drop-folder]').forEach((el) => el.classList.toggle('drop-active', el === target));
});

document.addEventListener('drop', (event) => {
    const target = event.target.closest?.('[data-drop-folder]');
    if (!target || !dragPayload) return;
    event.preventDefault();
    const targetId = target.dataset.dropFolder === 'root' ? null : Number(target.dataset.dropFolder);
    const payload = dragPayload;
    dragPayload = null;
    target.classList.remove('drop-active');

    if (payload.kind === 'folder' && targetId === payload.id) {
        notify('Folder tidak bisa dipindahkan ke dirinya sendiri');
        return;
    }
    if (payload.kind === 'file') {
        run(() => api(`/files/${payload.id}/move`, { method: 'POST', json: { folder_id: targetId } }), 'File dipindahkan');
    } else {
        run(() => api(`/folders/${payload.id}/move`, { method: 'POST', json: { parent_id: targetId } }), 'Folder dipindahkan');
    }
});
