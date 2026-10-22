// Interaksi RuangCloud (versi vanilla JS dari state React di prototipe Figma).
// Semua elemen interaktif memakai atribut data-action / id, tanpa library tambahan.

import { api, startUpload, toggleUpload, cancelUpload } from './uploader';

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

async function openPreview(fileId, name, mime, previewUrl, downloadUrl, updated = '') {
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

function addFolder(parentId) {
    const name = window.prompt('Nama folder baru:');
    if (!name || !name.trim()) return;
    run(
        () => api('/folders', { method: 'POST', json: { name: name.trim(), parent_id: parentId ? Number(parentId) : null } }),
        'Folder baru berhasil dibuat',
    );
}

function renameFolder(id, current) {
    const name = window.prompt('Nama folder:', current);
    if (!name || !name.trim() || name.trim() === current) return;
    run(() => api(`/folders/${id}`, { method: 'PATCH', json: { name: name.trim() } }), 'Nama folder diubah');
}

function deleteFolder(id, redirect) {
    if (!window.confirm('Hapus folder ini? Folder hanya bisa dihapus bila kosong.')) return;
    run(() => api(`/folders/${id}`, { method: 'DELETE' }), 'Folder dihapus', redirect);
}

let renameFileState = null;
function renameFile(id, name, baseName = '', extension = '', type = '') {
    const ext = extension || (name && name.includes('.') ? name.split('.').pop() : '');
    const base = baseName || (ext && name && name.toLowerCase().endsWith('.' + ext.toLowerCase()) ? name.slice(0, -(ext.length + 1)) : name);

    renameFileState = { id, ext, name };

    const modal = $('#rename-modal');
    if (!modal) {
        let input = window.prompt(`Nama file baru (${type || 'file'}):`, base || name);
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


function trashFile(id) {
    if (!window.confirm('Pindahkan file ini ke sampah?')) return;
    run(() => api(`/files/${id}`, { method: 'DELETE' }), 'File dipindahkan ke sampah');
}

let moveFileId = null;
function openMove(id) {
    moveFileId = id;
    $('#move-folder').value = '';
    $('#move-modal').hidden = false;
}
function confirmMove() {
    const folderId = $('#move-folder').value;
    $('#move-modal').hidden = true;
    run(
        () => api(`/files/${moveFileId}/move`, { method: 'POST', json: { folder_id: folderId ? Number(folderId) : null } }),
        'File dipindahkan',
    );
}

let shareFileId = null;
function openShare(id, name) {
    shareFileId = id;
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
        const data = await api(`/files/${shareFileId}/share`, {
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
        window.prompt('Salin tautan ini:', text);
    }
}

function revokeShare(id) {
    if (!window.confirm('Cabut tautan ini? Orang yang memegang tautan tidak bisa lagi mengunduh.')) return;
    run(() => api(`/shares/${id}`, { method: 'DELETE' }), 'Tautan dicabut');
}

function restoreFile(id) {
    run(() => api(`/trash/${id}/restore`, { method: 'POST' }), 'File dipulihkan');
}

function purgeFile(id) {
    if (!window.confirm('Hapus file ini secara permanen? Tindakan ini tidak bisa dibatalkan.')) return;
    run(() => api(`/trash/${id}`, { method: 'DELETE' }), 'File dihapus permanen');
}

function emptyTrash() {
    if (!window.confirm('Kosongkan sampah? Semua file di dalamnya dihapus permanen.')) return;
    run(() => api('/trash', { method: 'DELETE' }), 'Sampah dikosongkan');
}

/* ---------- Menu titik tiga pada file ---------- */
const closeMenus = () => $$('details.menu[open]').forEach((menu) => (menu.open = false));

// Panel menu memakai position:fixed agar tidak terpotong tabel yang bisa di-scroll.
document.addEventListener('toggle', (event) => {
    const menu = event.target;
    if (!(menu instanceof HTMLDetailsElement) || !menu.classList.contains('menu') || !menu.open) return;

    $$('details.menu[open]').forEach((other) => {
        if (other !== menu) other.open = false;
    });

    const rect = $('summary', menu).getBoundingClientRect();
    const panel = $('[data-menu-panel]', menu);
    const width = 200;
    panel.style.width = `${width}px`;
    panel.style.left = `${Math.max(8, Math.min(rect.right - width, window.innerWidth - width - 8))}px`;
    const below = rect.bottom + 4;
    panel.style.top = `${below + panel.offsetHeight > window.innerHeight - 8 ? Math.max(8, rect.top - panel.offsetHeight - 4) : below}px`;
}, true);

document.addEventListener('click', (event) => {
    if (!event.target.closest('details.menu') || event.target.closest('[data-action]')) closeMenus();
});
window.addEventListener('scroll', closeMenus, true);
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
        case 'toggle-upload': toggleUpload(); break;
        case 'cancel-upload': cancelUpload(); break;
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
        case 'confirm-move':  confirmMove(); break;
        case 'share-file':    openShare(target.dataset.fileId, target.dataset.name); break;
        case 'create-share':  createShare(); break;
        case 'copy-share':    copyText($('#share-url').value); break;
        case 'copy-link':     copyText(target.dataset.url); break;
        case 'revoke-share':  revokeShare(target.dataset.shareId); break;
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
            const input = $('#password');
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
        const file = event.target.files[0];
        event.target.value = '';
        setUploadModal(false);
        if (file) startUpload(file);
    }
});

// Drag & drop ke area unggah.
document.addEventListener('dragover', (event) => {
    if (event.target.closest?.('#drop-zone')) event.preventDefault();
});
document.addEventListener('drop', (event) => {
    if (!event.target.closest?.('#drop-zone')) return;
    event.preventDefault();
    const file = event.dataTransfer?.files?.[0];
    setUploadModal(false);
    if (file) startUpload(file);
});

/* ---------- Halaman login: pilih peran ---------- */
function setRole(role) {
    const form = $('#login-form');
    if (!form) return;
    const admin = role === 'admin';

    $('#role-input').value = role;
    $('#email').value = admin ? 'admin@ruangcloud.id' : 'naufal@ruangcloud.id';
    $('#submit-label').textContent = admin ? 'Administrator' : 'Pengguna';

    $$('[data-role]').forEach((button) => {
        const on = button.dataset.role === role;
        button.classList.toggle('bg-white', on);
        button.classList.toggle('text-[#098568]', on);
        button.classList.toggle('shadow-sm', on);
        button.classList.toggle('text-[#75817d]', !on);
    });
}

renderFiles();
