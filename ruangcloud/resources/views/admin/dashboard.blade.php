@extends('layouts.app')

@section('title', 'Admin Console — RuangCloud')

@section('content')
<div class="min-h-screen bg-[#f8faf9] text-slate-900">
  <button type="button" id="sidebar-backdrop" hidden aria-label="Tutup menu" data-action="close-sidebar"
          class="fixed inset-0 z-30 bg-slate-900/30 backdrop-blur-sm lg:hidden"></button>

  {{-- ══ SIDEBAR ══ --}}
  <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-[264px] -translate-x-full flex-col bg-[#0f2e26] px-5 pb-5 pt-6 text-white transition-transform duration-300 lg:translate-x-0">
    <div class="mb-2 flex items-center justify-between px-1">
      <x-brand :light="true" />
      <button type="button" data-action="close-sidebar" class="lg:hidden rounded-lg p-1.5 hover:bg-white/10">
        <x-icon name="x" class="h-5 w-5" />
      </button>
    </div>
    <div class="mb-6 ml-[46px] inline-flex w-fit rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-300">Admin Console</div>

    <nav class="space-y-1.5" id="admin-nav">
      @foreach ($navItems as $index => $item)
        <button type="button"
                data-panel="{{ $item['label'] }}"
                class="admin-nav-btn flex h-11 w-full items-center gap-3.5 rounded-xl px-3.5 text-sm font-semibold transition-all {{ $index === 0 ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-950/20 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
          <x-icon :name="$item['icon']" class="h-5 w-5" />
          {{ $item['label'] }}
          @isset($item['badge'])
            <span class="ml-auto rounded-full bg-rose-500 px-2 py-0.5 text-xs font-bold text-white">{{ $item['badge'] }}</span>
          @endisset
        </button>
      @endforeach
    </nav>

    <div class="mt-auto rounded-2xl border border-white/10 bg-white/5 p-4">
      <div class="mb-2 flex items-center justify-between">
        <span class="text-xs font-bold text-slate-200">Kesehatan Sistem</span>
        <span class="relative flex h-2.5 w-2.5">
          <span class="absolute inline-flex h-full w-full animate-ping rounded-full {{ $nodesActive === $nodesTotal ? 'bg-emerald-400' : 'bg-rose-400' }} opacity-75"></span>
          <span class="relative inline-flex h-2.5 w-2.5 rounded-full {{ $nodesActive === $nodesTotal ? 'bg-emerald-400' : 'bg-rose-500' }}"></span>
        </span>
      </div>
      <p class="text-xs leading-relaxed text-slate-300">{{ $nodesActive }} dari {{ $nodesTotal }} node penyimpanan aktif beroperasi normal.</p>
    </div>

    <button type="button" data-action="open-admin-logout" class="mt-4 flex w-full items-center gap-3 rounded-2xl border border-transparent p-2 text-left hover:bg-white/10 transition-colors">
      <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-sm font-extrabold text-white">
        {{ \App\Support\Fmt::initials(auth()->user()->name) }}
      </div>
      <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-bold text-white">{{ auth()->user()->name }}</p>
        <p class="truncate text-xs text-slate-300">{{ auth()->user()->email }}</p>
      </div>
      <span class="rounded-lg bg-rose-500/20 px-2.5 py-1 text-xs font-bold text-rose-300 transition hover:bg-rose-500/30">Keluar</span>
    </button>
  </aside>

  {{-- ══ MAIN CONTENT ══ --}}
  <div class="lg:pl-[264px]">
    {{-- Header --}}
    <header class="sticky top-0 z-20 flex h-[76px] items-center border-b border-slate-200/90 bg-white/95 px-5 backdrop-blur md:px-8">
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
          <input class="h-10 w-64 rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-3.5 text-xs font-medium text-slate-900 outline-none focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"
                 placeholder="Cari pengguna atau aktivitas...">
        </div>
        <button type="button" data-action="toast" data-message="Semua sistem beroperasi normal"
                class="relative rounded-xl p-2.5 text-slate-500 hover:bg-slate-100">
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
// ── Panel Navigation ──────────────────────────────────────────────
const navBtns   = document.querySelectorAll('.admin-nav-btn');
const panels    = document.querySelectorAll('.admin-panel');
const pageTitle = document.getElementById('page-title');

function switchPanel(label) {
  // Update nav buttons
  navBtns.forEach(btn => {
    const isActive = btn.dataset.panel === label;
    btn.classList.toggle('bg-emerald-600', isActive);
    btn.classList.toggle('text-white', isActive);
    btn.classList.toggle('shadow-lg', isActive);
    btn.classList.toggle('shadow-emerald-950/20', isActive);
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

// ── Mobile Sidebar Toggle ─────────────────────────────────────────
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

// ── Toast Notification ────────────────────────────────────────────
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

// ── Admin Logout Modal ────────────────────────────────────────────
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
