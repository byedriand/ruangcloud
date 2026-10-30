@extends('layouts.app')

@section('title', 'Admin Console â€” RuangCloud')

@section('content')
<div class="min-h-screen text-slate-900" style="background: radial-gradient(720px 420px at 85% -5%, rgba(52,211,153,.20), transparent 60%), radial-gradient(640px 480px at -10% 30%, rgba(45,212,191,.14), transparent 60%), radial-gradient(560px 420px at 55% 110%, rgba(16,185,129,.12), transparent 60%), linear-gradient(165deg, #f4faf7 0%, #eaf5ef 48%, #e6f4ee 100%);">
  <button type="button" id="sidebar-backdrop" hidden aria-label="Tutup menu" data-action="close-sidebar"
          class="fixed inset-0 z-30 bg-slate-900/30 backdrop-blur-sm lg:hidden"></button>

  {{-- â•â• SIDEBAR â•â• --}}
  <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-[264px] -translate-x-full flex-col border-r border-white/10 bg-gradient-to-b from-[#124534]/95 via-[#0b271e]/[0.94] to-[#061a13]/[0.97] px-5 pb-5 pt-6 shadow-[24px_0_70px_-24px_rgba(4,32,24,0.6)] backdrop-blur-2xl text-white transition-transform duration-300 lg:translate-x-0">
    <div class="mb-2 flex items-center justify-between px-1">
      <x-brand :light="true" />
      <button type="button" data-action="close-sidebar" class="lg:hidden rounded-lg p-1.5 hover:bg-white/10">
        <x-icon name="x" class="h-5 w-5" />
      </button>
    </div>
    <div class="mb-6 ml-[46px] inline-flex w-fit rounded-full bg-gradient-to-r from-emerald-400/25 to-teal-400/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-200 ring-1 ring-emerald-300/30">Admin Console</div>

    <nav class="space-y-2" id="admin-nav">
      @foreach ($navItems as $index => $item)
        <button type="button"
                data-panel="{{ $item['label'] }}"
                class="admin-nav-btn flex h-11 w-full items-center gap-3.5 rounded-xl px-3.5 text-sm font-semibold transition-all border border-white/[0.07] bg-white/[0.04] shadow-[inset_0_1px_0_rgba(255,255,255,0.05)] backdrop-blur-md {{ $index === 0 ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg shadow-emerald-500/25 ring-1 ring-white/20 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
          <x-icon :name="$item['icon']" class="h-5 w-5" />
          {{ $item['label'] }}
          @isset($item['badge'])
            <span class="ml-auto rounded-full bg-rose-500 px-2 py-0.5 text-xs font-bold text-white">{{ $item['badge'] }}</span>
          @endisset
        </button>
      @endforeach
    </nav>

  
    <a href="{{ route('profile.edit') }}" class="mt-auto flex w-full items-center gap-3 rounded-2xl border border-white/[0.08] bg-white/[0.05] p-2.5 text-sm font-semibold text-slate-200 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] backdrop-blur-md transition-all hover:bg-white/10 hover:text-white">
      <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/[0.07] text-emerald-300 ring-1 ring-white/10"><x-icon name="user" class="h-4.5 w-4.5" /></span>
      Profil Saya
    </a>

    <button type="button" data-action="open-admin-logout" class="mt-2 flex w-full items-center gap-3 rounded-2xl border border-white/[0.06] bg-white/[0.04] p-2 text-left backdrop-blur-md transition-colors hover:bg-white/10">
      @if (auth()->user()->avatarUrl())
        <img src="{{ auth()->user()->avatarUrl() }}" alt="Foto profil" class="h-10 w-10 shrink-0 rounded-xl object-cover">
      @else
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 text-sm font-extrabold text-white shadow-lg shadow-emerald-950/30 ring-2 ring-white/25">
          {{ \App\Support\Fmt::initials(auth()->user()->name) }}
        </div>
      @endif
      <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-bold text-white">{{ auth()->user()->name }}</p>
      </div>
      <span class="rounded-lg bg-rose-500/20 px-2.5 py-1 text-xs font-bold text-rose-300 transition hover:bg-rose-500/30">Keluar</span>
    </button>
  </aside>

  {{-- â•â• MAIN CONTENT â•â• --}}
  <div class="lg:pl-[264px]">
    {{-- Header --}}
    <header class="sticky top-0 z-20 flex h-[76px] items-center border-b border-white/60 bg-white/70 px-5 shadow-[0_8px_30px_-12px_rgba(6,40,30,0.12)] backdrop-blur-xl md:px-8">
      <button type="button" data-action="open-sidebar" class="mr-3 rounded-xl p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
        <x-icon name="menu" class="h-6 w-6" />
      </button>
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-emerald-600">Admin Console</p>
        <h1 id="page-title" class="mt-0.5 text-lg font-extrabold text-slate-900 md:text-xl">Ringkasan</h1>
      </div>
      <div class="ml-auto flex items-center gap-3">
        <div class="relative hidden sm:block">
          <x-icon name="search" class="absolute left-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400" />
          <input class="h-10 w-64 rounded-xl border border-white/60 bg-gradient-to-r from-white/75 via-white/60 to-emerald-100/40 pl-10 pr-3.5 text-xs font-medium text-slate-900 shadow-[inset_0_1px_0_rgba(255,255,255,0.95),0_10px_28px_-12px_rgba(6,40,30,0.28)] outline-none backdrop-blur-xl transition placeholder:text-slate-500 focus:border-emerald-400 focus:from-white/90 focus:to-white/85 focus:ring-4 focus:ring-emerald-500/15"
                 placeholder="Cari pengguna atau aktivitas...">
        </div>
        <button type="button" data-action="toast" data-message="Semua sistem beroperasi normal"
                class="relative rounded-2xl border border-white/60 bg-white/60 p-2.5 text-slate-600 shadow-[inset_0_1px_0_rgba(255,255,255,0.9),0_8px_20px_-10px_rgba(6,40,30,0.25)] backdrop-blur-xl transition hover:bg-white/85 hover:text-emerald-700">
          <x-icon name="bell" class="h-5 w-5" />
          <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
        </button>
      </div>
    </header>

    {{-- Panels --}}
    <main class="mx-auto max-w-[1440px] p-5 md:p-8">

      {{-- Panel: Ringkasan --}}
      <div id="panel-Ringkasan" class="admin-panel">
        @include('admin.partials.panel-ringkasan')
      </div>

      {{-- Panel: Pengguna --}}
      <div id="panel-Pengguna" class="admin-panel hidden">
        @include('admin.partials.panel-pengguna')
      </div>

      {{-- Panel: Penyimpanan --}}
      <div id="panel-Penyimpanan" class="admin-panel hidden">
        @include('admin.partials.panel-penyimpanan')
      </div>

      {{-- Panel: Unggahan --}}
      <div id="panel-Unggahan" class="admin-panel hidden">
        @include('admin.partials.panel-unggahan')
      </div>

      {{-- Panel: Audit log --}}
      <div id="panel-Audit log" class="admin-panel hidden">
        @include('admin.partials.panel-auditlog')
      </div>

    </main>
  </div>
</div>

{{-- Toast --}}
@include('components.success-popup')
<div id="toast" hidden class="fixed left-1/2 top-6 z-[60] -translate-x-1/2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-2xl"></div>

{{-- Modal Konfirmasi Keluar Admin --}}
<div id="admin-logout-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
  <div class="w-full max-w-[400px] rounded-3xl bg-[#0f2e26] border border-white/10 p-7 text-center text-white shadow-2xl">
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-rose-400">
      <x-icon name="logout" class="h-7 w-7" />
    </div>
    <h2 class="text-xl font-extrabold tracking-tight">Konfirmasi Keluar</h2>
    <p class="mt-2 text-sm text-slate-300">Apakah Anda yakin ingin mengakhiri sesi Admin Console?</p>
    <div class="mt-6 flex gap-3">
      <button type="button" data-action="close-admin-logout" class="flex h-11 flex-1 items-center justify-center rounded-xl border border-white/15 text-sm font-bold text-slate-200 hover:bg-white/10 transition-colors">Batal</button>
      <form method="POST" action="{{ route('logout') }}" class="flex-1">
        @csrf
        <button type="submit" class="flex h-11 w-full items-center justify-center rounded-xl bg-rose-600 text-sm font-bold text-white shadow-lg shadow-rose-600/30 hover:bg-rose-500 transition-colors">Ya, Keluar</button>
      </form>
    </div>
  </div>
</div>

<script>
// â”€â”€ Dialog konfirmasi bertema (pengganti confirm bawaan browser) â”€â”€
function rcAdminConfirm(message, danger = false) {
  return new Promise((resolve) => {
    document.getElementById('rc-admin-dialog')?.remove();
    const wrap = document.createElement('div');
    wrap.id = 'rc-admin-dialog';
    wrap.className = 'fixed inset-0 z-[90] flex items-center justify-center bg-[#04120d]/60 p-4 backdrop-blur-sm';
    const icon = danger
      ? '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>'
      : '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>';
    wrap.innerHTML = `
      <div class="w-full max-w-sm rounded-[24px] border border-white/15 bg-white/10 p-6 text-center shadow-[0_30px_80px_rgba(0,0,0,0.45)] backdrop-blur-xl">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl text-white shadow-lg ${danger ? 'bg-gradient-to-br from-rose-400 to-rose-600 shadow-rose-950/40' : 'bg-gradient-to-br from-emerald-400 to-teal-600 shadow-emerald-950/40'}">${icon}</div>
        <p class="text-sm font-semibold leading-relaxed text-white"></p>
        <div class="mt-6 flex gap-3">
          <button type="button" data-rc-cancel class="flex-1 rounded-xl border border-white/15 bg-white/[0.06] px-4 py-2.5 text-sm font-bold text-slate-200 backdrop-blur-md transition hover:bg-white/10">Batal</button>
          <button type="button" data-rc-ok class="flex-1 rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-lg transition ${danger ? 'bg-gradient-to-r from-rose-500 to-rose-600 shadow-rose-950/40 hover:brightness-110' : 'bg-gradient-to-r from-emerald-500 to-teal-500 shadow-emerald-950/40 hover:brightness-110'}">Ya, Lanjutkan</button>
        </div>
      </div>`;
    wrap.querySelector('p').textContent = message;
    document.body.appendChild(wrap);
    document.body.style.overflow = 'hidden';
    const done = (val) => { wrap.remove(); document.body.style.overflow = ''; document.removeEventListener('keydown', onKey); resolve(val); };
    const onKey = (e) => { if (e.key === 'Escape') done(false); if (e.key === 'Enter') done(true); };
    document.addEventListener('keydown', onKey);
    wrap.addEventListener('click', (e) => { if (e.target === wrap) done(false); });
    wrap.querySelector('[data-rc-cancel]').addEventListener('click', () => done(false));
    wrap.querySelector('[data-rc-ok]').addEventListener('click', () => done(true));
  });
}

// â”€â”€ Panel Navigation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
const navBtns   = document.querySelectorAll('.admin-nav-btn');
const panels    = document.querySelectorAll('.admin-panel');
const pageTitle = document.getElementById('page-title');

function switchPanel(label) {
  // Update nav buttons
  navBtns.forEach(btn => {
    const isActive = btn.dataset.panel === label;
    btn.classList.toggle('bg-gradient-to-r', isActive);
    btn.classList.toggle('from-emerald-500', isActive);
    btn.classList.toggle('to-teal-500', isActive);
    btn.classList.toggle('text-white', isActive);
    btn.classList.toggle('shadow-lg', isActive);
    btn.classList.toggle('shadow-emerald-500/25', isActive);
    btn.classList.toggle('ring-1', isActive);
    btn.classList.toggle('ring-white/20', isActive);
    btn.classList.toggle('font-bold', isActive);
    btn.classList.toggle('text-slate-300', !isActive);
    btn.classList.toggle('hover:bg-white/10', !isActive);
    btn.classList.toggle('hover:text-white', !isActive);
  });

  // Show/hide panels
  panels.forEach(panel => {
    panel.classList.toggle('hidden', panel.id !== 'panel-' + label);
  });

  // Update page title
  if (pageTitle) pageTitle.textContent = label;

  // Close sidebar on mobile
  closeSidebar();
}

navBtns.forEach(btn => {
  btn.addEventListener('click', () => switchPanel(btn.dataset.panel));
});

// â”€â”€ Mobile Sidebar Toggle â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
const sidebar         = document.getElementById('sidebar');
const sidebarBackdrop = document.getElementById('sidebar-backdrop');

function openSidebar() {
  sidebar?.classList.remove('-translate-x-full');
  sidebarBackdrop?.removeAttribute('hidden');
}

function closeSidebar() {
  sidebar?.classList.add('-translate-x-full');
  sidebarBackdrop?.setAttribute('hidden', '');
}

document.querySelectorAll('[data-action="open-sidebar"]').forEach(el => el.addEventListener('click', openSidebar));
document.querySelectorAll('[data-action="close-sidebar"]').forEach(el => el.addEventListener('click', closeSidebar));

// â”€â”€ Toast Notification â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function notify(msg) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.removeAttribute('hidden');
  setTimeout(() => t.setAttribute('hidden', ''), 3000);
}
window.notify = notify;

document.querySelectorAll('[data-action="toast"]').forEach(el => {
  el.addEventListener('click', () => notify(el.dataset.message || 'Aksi berhasil'));
});

// â”€â”€ Admin Logout Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
const adminLogoutModal = document.getElementById('admin-logout-modal');
document.querySelectorAll('[data-action="open-admin-logout"]').forEach(el => {
  el.addEventListener('click', () => adminLogoutModal?.removeAttribute('hidden'));
});
document.querySelectorAll('[data-action="close-admin-logout"]').forEach(el => {
  el.addEventListener('click', () => adminLogoutModal?.setAttribute('hidden', ''));
});
adminLogoutModal?.addEventListener('click', (e) => {
  if (e.target === adminLogoutModal) adminLogoutModal.setAttribute('hidden', '');
});
</script>
@endsection
