@php
    $avatarTone = [
        'mint'     => 'bg-[#dff6ef] text-[#087d62]',
        'lavender' => 'bg-[#ece8ff] text-[#6e5ec2]',
        'amber'    => 'bg-[#fff0d9] text-[#b1711a]',
        'blue'     => 'bg-[#e7f0ff] text-[#4777bc]',
    ];
@endphp

<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
  <div>
    <h2 class="text-2xl font-extrabold tracking-[-0.035em]">Selamat datang, Admin</h2>
    <p class="mt-1 text-xs text-[#7b8782]">Pantau performa RuangCloud dan kelola akses pengguna.</p>
  </div>
  <button type="button" data-action="toast" data-message="Laporan sistem sedang disiapkan"
          class="flex h-10 items-center gap-2 rounded-xl border border-[#d9e3df] bg-white px-4 text-[10px] font-extrabold text-[#46544f] hover:border-[#0cb48c] transition-colors">
    <x-icon name="download" class="h-4 w-4" /> Unduh laporan
  </button>
</div>

{{-- Stat Cards --}}
<section class="mb-5 grid grid-cols-2 gap-3 xl:grid-cols-4">
  @foreach ($stats as $stat)
    <article class="rounded-[18px] border border-[#e0e8e5] bg-white p-4 md:p-5 hover:shadow-md transition-shadow">
      <div class="mb-4 flex items-center justify-between">
        <div class="folder-icon folder-{{ $stat['tone'] }}"><x-icon :name="$stat['icon']" class="h-5 w-5" /></div>
        <span class="rounded-full bg-[#ebfaf6] px-2 py-1 text-[8px] font-extrabold text-[#099273]">Normal</span>
      </div>
      <p class="text-[10px] font-semibold text-[#7a8782]">{{ $stat['label'] }}</p>
      <p class="mt-1 text-xl font-extrabold tracking-[-0.035em] md:text-2xl">{{ $stat['value'] }}</p>
      <p class="mt-1 text-[9px] text-[#909a96]">{{ $stat['note'] }}</p>
    </article>
  @endforeach
</section>

{{-- Charts Row --}}
<section class="mb-5 grid gap-4 xl:grid-cols-[1.55fr_1fr]">
  <article class="rounded-[20px] border border-[#e0e8e5] bg-white p-5">
    <div class="mb-6 flex items-start justify-between">
      <div>
        <h3 class="text-sm font-extrabold">Aktivitas unggahan</h3>
        <p class="mt-1 text-[9px] text-[#8b9692]">Volume file yang berhasil diunggah minggu ini</p>
      </div>
      <button type="button" class="rounded-lg border border-[#e2e8e5] px-2.5 py-1.5 text-[9px] font-bold text-[#697671]">7 hari terakhir</button>
    </div>
    <div class="flex h-48 items-end gap-2 border-b border-[#e5ebe8] pt-5">
      @foreach ($chartValues as $i => $value)
        <div class="group relative flex h-full flex-1 items-end">
          <div class="w-full rounded-t-md bg-[#d8f1ea] transition group-hover:bg-[#0cb48c]" style="height: {{ $value }}%">
            <span class="absolute -top-4 left-1/2 hidden -translate-x-1/2 text-[7px] font-bold group-hover:block">{{ $chartTips[$i] }}</span>
          </div>
        </div>
      @endforeach
    </div>
    <div class="mt-3 flex justify-between text-[8px] font-semibold text-[#929c98]">
      @foreach ($chartLabels as $label)<span>{{ $label }}</span>@endforeach
    </div>
  </article>

  <article class="rounded-[20px] border border-[#e0e8e5] bg-white p-5">
    <div class="mb-5 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-extrabold">Storage node</h3>
        <p class="mt-1 text-[9px] text-[#8b9692]">Status replikasi real-time</p>
      </div>
      <span class="rounded-full bg-[#e8faf5] px-2 py-1 text-[8px] font-extrabold text-[#099273]">{{ $nodesActive }} aktif</span>
    </div>
    <div class="space-y-3">
      @foreach ($nodes as $node)
        <div class="rounded-xl border border-[#e8eeeb] p-3">
          <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#e9f8f4] text-[#0aa982]">
              <x-icon name="archive" class="h-4 w-4" />
            </div>
            <div class="flex-1">
              <div class="flex justify-between text-[9px]">
                <span class="font-extrabold">Node {{ $node['name'] }}</span>
                <span class="text-[#84908c]">{{ $node['size'] }}</span>
              </div>
              <div class="mt-2 h-1.5 rounded-full bg-[#edf1ef]">
                <div class="h-full rounded-full bg-[#0cb48c]" style="width: {{ $node['usage'] }}%"></div>
              </div>
            </div>
            <span class="h-2 w-2 rounded-full {{ $node['status'] === 'active' ? 'bg-[#29cfa5]' : 'bg-[#ff776e]' }}"></span>
          </div>
        </div>
      @endforeach
    </div>
  </article>
</section>

{{-- Bottom Row --}}
<section class="grid gap-4 xl:grid-cols-[1.45fr_1fr]">
  <article class="overflow-hidden rounded-[20px] border border-[#e0e8e5] bg-white">
    <div class="flex items-center justify-between border-b border-[#e8edeb] px-5 py-4">
      <div>
        <h3 class="text-sm font-extrabold">Pengguna terbaru</h3>
        <p class="mt-1 text-[9px] text-[#8b9692]">Kelola peran dan kuota pengguna</p>
      </div>
      <button type="button" data-action="nav" data-nav="Pengguna"
              class="text-[9px] font-extrabold text-[#0aa982]">Lihat semua</button>
    </div>
    <div class="overflow-x-auto"><div class="min-w-[580px]">
      <div class="grid grid-cols-[1.4fr_1fr_110px_30px] gap-3 px-5 py-2.5 text-[8px] font-extrabold uppercase tracking-[.1em] text-[#9aa39f]">
        <span>Pengguna</span><span>Kuota</span><span>Status</span><span></span>
      </div>
      @foreach ($users as $user)
        <div class="grid grid-cols-[1.4fr_1fr_110px_30px] items-center gap-3 border-t border-[#edf1ef] px-5 py-3 hover:bg-[#fafcfb]">
          <div class="flex items-center gap-2.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-full text-[9px] font-extrabold {{ $avatarTone[$user['tone']] }}">{{ $user['initials'] }}</div>
            <div>
              <p class="text-[10px] font-extrabold">{{ $user['name'] }}</p>
              <p class="mt-0.5 text-[8px] text-[#8c9792]">{{ $user['email'] }}</p>
            </div>
          </div>
          <div>
            <div class="mb-1 flex justify-between text-[8px]"><span>{{ $user['used'] }}</span><span class="text-[#9aa39f]">{{ $user['total'] }}</span></div>
            <div class="h-1.5 rounded-full bg-[#ebefed]">
              <div class="h-full rounded-full {{ $user['percent'] > 75 ? 'bg-[#f0a545]' : 'bg-[#0cb48c]' }}" style="width: {{ $user['percent'] }}%"></div>
            </div>
          </div>
          <span class="w-fit rounded-full px-2 py-1 text-[8px] font-extrabold {{ $user['status'] === 'Aktif' ? 'bg-[#e9faf5] text-[#098b6c]' : 'bg-[#f0f2f1] text-[#7d8985]' }}">{{ $user['status'] }}</span>
          <button type="button" data-action="toast" data-message="{{ $user['name'] }} dipilih">
            <x-icon name="more" class="h-4 w-4 text-[#8c9692]" />
          </button>
        </div>
      @endforeach
    </div></div>
  </article>

  <article class="rounded-[20px] border border-[#e0e8e5] bg-white p-5">
    <div class="mb-5 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-extrabold">Aktivitas terbaru</h3>
        <p class="mt-1 text-[9px] text-[#8b9692]">Audit log sistem</p>
      </div>
      <button type="button" data-action="nav" data-nav="Audit log">
        <x-icon name="chevron" class="h-4 w-4 text-[#82908b]" />
      </button>
    </div>
    <div class="space-y-4">
      @forelse ($activities as $activity)
        <div class="flex gap-3">
          <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#eef7f4] text-[#0aa982]">
            <x-icon :name="$activity['icon']" class="h-3.5 w-3.5" />
          </div>
          <div class="min-w-0">
            <p class="truncate text-[9px] font-bold">{{ $activity['text'] }}</p>
            <p class="mt-1 text-[8px] text-[#929c98]">{{ $activity['time'] }}</p>
          </div>
        </div>
      @empty
        <p class="text-[9px] text-[#929c98]">Belum ada aktivitas.</p>
      @endforelse
    </div>
  </article>
</section>
