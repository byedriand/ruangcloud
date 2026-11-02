@php
    $avatarTone = [
        'mint'     => 'bg-gradient-to-br from-[#c9f5e6] to-[#8fe3c5] text-[#087d62] shadow-[inset_0_1px_0_rgba(255,255,255,0.7)]',
        'lavender' => 'bg-gradient-to-br from-[#e2dbff] to-[#bdb0f5] text-[#5a4bb0] shadow-[inset_0_1px_0_rgba(255,255,255,0.7)]',
        'amber'    => 'bg-gradient-to-br from-[#ffe9c2] to-[#ffd08a] text-[#9a5f10] shadow-[inset_0_1px_0_rgba(255,255,255,0.7)]',
        'blue'     => 'bg-gradient-to-br from-[#d6e6ff] to-[#a8c8f5] text-[#35619f] shadow-[inset_0_1px_0_rgba(255,255,255,0.7)]',
    ];
@endphp

<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
  <div>
    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Ringkasan Sistem</h2>
    <p class="mt-1 text-sm text-slate-500">Pantau performa RuangCloud, kapasitas storage node, dan aktivitas pengguna.</p>
  </div>
  <button type="button" data-action="toast" data-message="Laporan sistem sedang disiapkan"
          class="flex h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white/70 backdrop-blur-md px-4.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
    <x-icon name="download" class="h-4 w-4 text-emerald-600" /> Unduh laporan
  </button>
</div>

{{-- Stat Cards --}}
<section class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-4">
  @foreach ($stats as $stat)
    <article class="rounded-2xl glass-card stat-lift p-5 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <div class="folder-icon folder-{{ $stat['tone'] }} h-11 w-11 rounded-xl flex items-center justify-center">
          <x-icon :name="$stat['icon']" class="h-5 w-5" />
        </div>
        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">Normal</span>
      </div>
      <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $stat['label'] }}</p>
      <p class="mt-1.5 text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">{{ $stat['value'] }}</p>
      <p class="mt-1 text-xs text-slate-500">{{ $stat['note'] }}</p>
    </article>
  @endforeach
</section>

{{-- Charts Row --}}
<section class="mb-6 grid gap-5 xl:grid-cols-[1.55fr_1fr]">
  <article class="rounded-3xl glass-card p-6 shadow-sm">
    <div class="mb-6 flex items-start justify-between">
      <div>
        <h3 class="text-base font-extrabold text-slate-900">Aktivitas Unggahan</h3>
        <p class="mt-0.5 text-xs text-slate-500">Volume berkas yang berhasil diunggah minggu ini</p>
      </div>
      <button type="button" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-100">7 hari terakhir</button>
    </div>
    <div class="flex h-48 items-end gap-3 border-b border-slate-100 pt-5">
      @foreach ($chartValues as $i => $value)
        <div class="group relative flex h-full flex-1 items-end">
          <div class="w-full rounded-t-lg bg-gradient-to-t from-emerald-500/60 via-emerald-400/70 to-teal-300 shadow-[inset_0_1px_0_rgba(255,255,255,0.5)] transition-all group-hover:from-emerald-500 group-hover:via-emerald-400 group-hover:to-teal-300" style="height: {{ max(6, $value) }}%">
            <span class="absolute -top-6 left-1/2 hidden -translate-x-1/2 rounded bg-slate-900 px-2 py-0.5 text-xs font-bold text-white group-hover:block">{{ $chartTips[$i] }}</span>
          </div>
        </div>
      @endforeach
    </div>
    <div class="mt-3 flex justify-between text-xs font-medium text-slate-500">
      @foreach ($chartLabels as $label)<span>{{ $label }}</span>@endforeach
    </div>
  </article>

  <article class="rounded-3xl glass-card p-6 shadow-sm">
    <div class="mb-5 flex items-center justify-between">
      <div>
        <h3 class="text-base font-extrabold text-slate-900">Storage Node</h3>
        <p class="mt-0.5 text-xs text-slate-500">Status replikasi & kapasitas real-time</p>
      </div>
      <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ $nodesActive }} aktif</span>
    </div>
    <div class="space-y-3.5">
      @foreach ($nodes as $node)
        <div class="rounded-2xl border border-white/50 bg-white/45 p-3.5 shadow-[inset_0_1px_0_rgba(255,255,255,0.6)] backdrop-blur-sm">
          <div class="flex items-center gap-3.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-lg shadow-emerald-500/30">
              <x-icon name="archive" class="h-5 w-5" />
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex justify-between text-xs">
                <span class="font-bold text-slate-800">Node {{ $node['name'] }}</span>
                <span class="font-medium text-slate-500">{{ $node['size'] }}</span>
              </div>
              <div class="mt-2 h-2 rounded-full bg-slate-200">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500" style="width: {{ max(4, $node['usage']) }}%"></div>
              </div>
            </div>
            <span class="h-2.5 w-2.5 rounded-full {{ $node['status'] === 'active' ? 'bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.18)]' : 'bg-rose-500 shadow-[0_0_0_4px_rgba(244,63,94,0.18)]' }}"></span>
          </div>
        </div>
      @endforeach
    </div>
  </article>
</section>

{{-- Bottom Row --}}
<section class="grid gap-5 xl:grid-cols-[1.5fr_1fr]">
  <article class="overflow-hidden rounded-3xl glass-card shadow-sm">
    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4.5">
      <div>
        <h3 class="text-base font-extrabold text-slate-900">Pengguna Terbaru</h3>
        <p class="mt-0.5 text-xs text-slate-500">Kelola kuota dan hak akses pengguna</p>
      </div>
      <button type="button" data-action="nav" data-nav="Pengguna"
              class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline">Lihat semua</button>
    </div>
    <div class="overflow-x-auto"><div class="min-w-[600px]">
      <div class="grid grid-cols-[1.4fr_1fr_110px_40px] gap-3 bg-gradient-to-r from-emerald-100/50 via-white/30 to-teal-100/40 px-6 py-3 text-xs font-bold uppercase tracking-wider text-slate-500">
        <span>Pengguna</span><span>Kuota</span><span>Status</span><span></span>
      </div>
      @foreach ($users as $user)
        <div class="grid grid-cols-[1.4fr_1fr_110px_40px] items-center gap-3 border-t border-slate-100 px-6 py-3.5 hover:bg-emerald-50/60 transition-colors">
          <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl text-xs font-extrabold {{ $avatarTone[$user['tone']] }}">{{ $user['initials'] }}</div>
            <div class="min-w-0">
              <p class="text-sm font-bold text-slate-900 truncate">{{ $user['name'] }}</p>
              <p class="mt-0.5 text-xs text-slate-500 truncate">{{ $user['email'] }}</p>
            </div>
          </div>
          <div>
            <div class="mb-1.5 flex justify-between text-xs font-medium text-slate-600"><span>{{ $user['used'] }}</span><span class="text-slate-400">{{ $user['total'] }}</span></div>
            <div class="h-2 rounded-full bg-slate-100">
              <div class="h-full rounded-full {{ $user['percent'] > 75 ? 'bg-amber-500' : 'bg-emerald-500' }}" style="width: {{ max(4, $user['percent']) }}%"></div>
            </div>
          </div>
          <span class="w-fit rounded-full px-2.5 py-1 text-xs font-bold {{ $user['status'] === 'Aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $user['status'] }}</span>
          <button type="button" data-action="toast" data-message="{{ $user['name'] }} dipilih" class="text-slate-400 hover:text-slate-700">
            <x-icon name="more" class="h-4.5 w-4.5" />
          </button>
        </div>
      @endforeach
    </div></div>
  </article>

  <article class="rounded-3xl glass-card p-6 shadow-sm">
    <div class="mb-5 flex items-center justify-between">
      <div>
        <h3 class="text-base font-extrabold text-slate-900">Aktivitas Terbaru</h3>
        <p class="mt-0.5 text-xs text-slate-500">Audit log riwayat sistem</p>
      </div>
      <button type="button" data-action="nav" data-nav="Audit log" class="rounded-lg p-1 text-slate-400 hover:text-slate-700">
        <x-icon name="chevron" class="h-4 w-4" />
      </button>
    </div>
    <div class="space-y-4">
      @forelse ($activities as $activity)
        <div class="flex items-center gap-3.5">
          <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-lg shadow-emerald-500/30">
            <x-icon :name="$activity['icon']" class="h-4 w-4" />
          </div>
          <div class="min-w-0 flex-1">
            <p class="truncate text-xs font-bold text-slate-800">{{ $activity['text'] }}</p>
            <p class="mt-0.5 text-xs text-slate-400">{{ $activity['time'] }}</p>
          </div>
        </div>
      @empty
        <p class="text-xs text-slate-500">Belum ada aktivitas.</p>
      @endforelse
    </div>
  </article>
</section>
