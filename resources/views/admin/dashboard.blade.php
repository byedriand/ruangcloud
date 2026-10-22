@extends('layouts.app')

@section('title', 'Admin Console — RuangCloud')

@section('content')
<div class="min-h-screen bg-[#f5f8f7] text-[#18231f]">
  <button type="button" id="sidebar-backdrop" hidden aria-label="Tutup menu" data-action="close-sidebar"
          class="fixed inset-0 z-30 bg-black/25 lg:hidden"></button>

  {{-- ══ SIDEBAR ══ --}}
  <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-[252px] -translate-x-full flex-col bg-[#12372e] px-4 pb-5 pt-6 text-white transition-transform duration-300 lg:translate-x-0">
    <div class="mb-2 flex items-center justify-between px-2">
      <x-brand :light="true" />
      <button type="button" data-action="close-sidebar" class="lg:hidden rounded-lg p-1 hover:bg-white/10">
        <x-icon name="x" />
      </button>
    </div>
    <div class="mb-7 ml-[52px] inline-flex w-fit rounded-full bg-[#0cb48c]/15 px-2 py-1 text-[8px] font-extrabold uppercase tracking-[.12em] text-[#67e0c1]">Admin Console</div>

    <nav class="space-y-1" id="admin-nav">
      @foreach ($navItems as $index => $item)
        <button type="button"
                data-panel="{{ $item['label'] }}"
                class="admin-nav-btn flex h-11 w-full items-center gap-3 rounded-xl px-3 text-xs font-bold transition {{ $index === 0 ? 'bg-[#0cb48c] text-white shadow-[0_8px_24px_rgba(12,180,140,.18)]' : 'text-[#a9c1ba] hover:bg-white/[.06] hover:text-white' }}">
          <x-icon :name="$item['icon']" class="h-[18px] w-[18px]" />
          {{ $item['label'] }}
          @isset($item['badge'])
            <span class="ml-auto rounded-full bg-[#ff776e] px-1.5 py-0.5 text-[8px] text-white">{{ $item['badge'] }}</span>
          @endisset
        </button>
      @endforeach
    </nav>

    <div class="mt-auto rounded-2xl border border-white/10 bg-white/[.04] p-3.5">
      <div class="mb-2 flex items-center justify-between">
        <span class="text-[10px] font-bold text-[#c4d6d1]">Kesehatan sistem</span>
        <span class="h-2 w-2 rounded-full {{ $nodesActive === $nodesTotal ? 'bg-[#34d4ac]' : 'bg-[#ff776e]' }} ring-4 ring-[#34d4ac]/10"></span>
      </div>
      <p class="text-[9px] leading-4 text-[#7fa298]">{{ $nodesActive }} dari {{ $nodesTotal }} storage node beroperasi normal.</p>
    </div>

    <button type="button" data-action="open-admin-logout" class="mt-4 flex w-full items-center gap-3 rounded-xl px-2 py-2.5 text-left hover:bg-white/[.05] transition-colors">
      <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0cb48c] text-[10px] font-extrabold">
        {{ \App\Support\Fmt::initials(auth()->user()->name) }}
      </div>
      <div>
        <p class="text-[10px] font-bold">{{ auth()->user()->name }}</p>
        <p class="text-[8px] text-[#86a59c]">{{ auth()->user()->email }}</p>
      </div>
      <span class="ml-auto rounded-lg bg-white/10 px-2.5 py-1 text-[9px] font-bold text-[#ff776e] transition hover:bg-white/20">Keluar</span>
    </button>
  </aside>

  {{-- ══ MAIN CONTENT ══ --}}
  <div class="lg:pl-[252px]">
    {{-- Header --}}
    <header class="sticky top-0 z-20 flex h-[72px] items-center border-b border-[#e2e9e6] bg-white/95 px-4 backdrop-blur md:px-7">
      <button type="button" data-action="open-sidebar" class="mr-3 rounded-lg p-2 hover:bg-[#f0f4f2] lg:hidden">
        <x-icon name="menu" />
      </button>
      <div>
        <p class="text-[9px] font-bold uppercase tracking-[.12em] text-[#0aa982]">Admin Console</p>
        <h1 id="page-title" class="mt-0.5 text-base font-extrabold">Ringkasan</h1>
      </div>
      <div class="ml-auto flex items-center gap-2">
        <div class="relative hidden sm:block">
          <x-icon name="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8d9793]" />
          <input class="h-9 w-56 rounded-xl bg-[#f3f6f5] pl-9 pr-3 text-[10px] outline-none focus:ring-2 focus:ring-[#0cb48c]/20"
                 placeholder="Cari pengguna atau aktivitas...">
        </div>
        <button type="button" data-action="toast" data-message="3 aktivitas baru perlu ditinjau"
                class="relative rounded-xl p-2.5 text-[#5e6c67] hover:bg-[#f0f4f2]">
          <x-icon name="bell" class="h-[19px] w-[19px]" />
          <span class="absolute right-2 top-2 h-1.5 w-1.5 rounded-full bg-[#ff6b62] ring-2 ring-white"></span>
        </button>
      </div>
    </header>

    {{-- Panels --}}
    <main class="mx-auto max-w-[1400px] p-4 md:p-7">

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
<div id="toast" hidden class="fixed left-1/2 top-5 z-[60] -translate-x-1/2 rounded-xl bg-[#18352d] px-4 py-3 text-xs font-bold text-white shadow-xl"></div>

{{-- Modal Konfirmasi Keluar Admin --}}
<div id="admin-logout-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
  <div class="w-full max-w-[380px] rounded-[24px] bg-[#12372e] border border-white/10 p-6 text-center text-white shadow-[0_30px_80px_rgba(0,0,0,.5)]">
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-[#ff776e]">
      <x-icon name="logout" class="h-6 w-6" />
    </div>
    <h2 class="text-lg font-extrabold tracking-[-0.02em]">Konfirmasi Keluar</h2>
    <p class="mt-1.5 text-xs text-[#a9c1ba]">Apakah Anda yakin ingin keluar dari sesi Admin Console?</p>
    <div class="mt-6 flex gap-2.5">
      <button type="button" data-action="close-admin-logout" class="flex h-11 flex-1 items-center justify-center rounded-xl border border-white/15 text-xs font-bold text-[#c7dad4] hover:bg-white/10 transition-colors">Batal</button>
      <form method="POST" action="{{ route('logout') }}" class="flex-1">
        @csrf
        <button type="submit" class="flex h-11 w-full items-center justify-center rounded-xl bg-[#e05252] text-xs font-extrabold text-white shadow-[0_4px_14px_rgba(224,82,82,.3)] hover:bg-[#c94141] transition-colors">Ya, Keluar</button>
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
    btn.classList.toggle('bg-[#0cb48c]', isActive);
    btn.classList.toggle('text-white', isActive);
    btn.classList.toggle('shadow-[0_8px_24px_rgba(12,180,140,.18)]', isActive);
    btn.classList.toggle('text-[#a9c1ba]', !isActive);
    btn.classList.toggle('hover:bg-white/[.06]', !isActive);
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

// Allow data-action="nav" data-nav="..." from within panels
document.addEventListener('click', e => {
  const trigger = e.target.closest('[data-action="nav"]');
  if (trigger && trigger.dataset.nav) {
    switchPanel(trigger.dataset.nav);
  }
});

// ── Sidebar Mobile ────────────────────────────────────────────────
const sidebar  = document.getElementById('sidebar');
const backdrop = document.getElementById('sidebar-backdrop');

function openSidebar() {
  sidebar.classList.replace('-translate-x-full', 'translate-x-0');
  backdrop.hidden = false;
}
function closeSidebar() {
  sidebar.classList.replace('translate-x-0', '-translate-x-full');
  backdrop.hidden = true;
}

document.addEventListener('click', e => {
  if (e.target.closest('[data-action="open-sidebar"]'))  openSidebar();
  if (e.target.closest('[data-action="close-sidebar"]')) closeSidebar();
});

// ── Modal Logout Admin ────────────────────────────────────────────
const adminLogoutModal = document.getElementById('admin-logout-modal');
document.addEventListener('click', e => {
  if (e.target.closest('[data-action="open-admin-logout"]')) {
    if (adminLogoutModal) adminLogoutModal.hidden = false;
  }
  if (e.target.closest('[data-action="close-admin-logout"]')) {
    if (adminLogoutModal) adminLogoutModal.hidden = true;
  }
});
if (adminLogoutModal) {
  adminLogoutModal.addEventListener('click', e => {
    if (e.target === adminLogoutModal) adminLogoutModal.hidden = true;
  });
}

// ── Toast ─────────────────────────────────────────────────────────
const toast = document.getElementById('toast');
let toastTimer;
document.addEventListener('click', e => {
  const btn = e.target.closest('[data-action="toast"]');
  if (!btn) return;
  clearTimeout(toastTimer);
  toast.textContent = btn.dataset.message;
  toast.hidden = false;
  toastTimer = setTimeout(() => { toast.hidden = true; }, 3000);
});
</script>
@endsection
