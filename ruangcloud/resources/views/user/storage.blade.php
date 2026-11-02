<x-user-layout title="Detail Penyimpanan" active="storage">
  <div class="mx-auto w-full max-w-5xl">
    <div class="mb-7">
      <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-emerald-700">
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
        Ruang kerja saya
      </span>
      <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Detail Penyimpanan</h1>
      <p class="mt-1.5 text-sm text-slate-500">Lihat file apa saja yang memakai kuotamu, sisa ruangmu, dan di node mana salinannya tersimpan.</p>
    </div>

    {{-- ── Ringkasan kuota ── --}}
    <section class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#0b2921] via-[#0f3d2e] to-teal-800 text-white shadow-xl shadow-emerald-950/20">
      <div class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full bg-emerald-400/15 blur-2xl"></div>
      <div class="pointer-events-none absolute -bottom-28 left-1/3 h-56 w-56 rounded-full bg-teal-300/10 blur-2xl"></div>
      <div class="relative p-7 md:p-9">
        <div class="flex flex-wrap items-end justify-between gap-4">
          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-200/80">Total terpakai</p>
            <p class="mt-1.5 text-4xl font-black tracking-tight md:text-5xl">{{ $summary['used'] }}<span class="ml-2 align-middle text-base font-bold text-emerald-100/70">dari {{ $summary['quota'] }}</span></p>
          </div>
          <span class="rounded-2xl bg-white/10 px-4 py-2 text-2xl font-black ring-1 ring-white/15">{{ $summary['percent'] }}%</span>
        </div>
        <div class="mt-5 h-3 overflow-hidden rounded-full bg-white/15">
          <div class="h-full rounded-full bg-gradient-to-r from-emerald-300 to-teal-200 transition-all duration-500" style="width: {{ $summary['percent'] }}%"></div>
        </div>
        <div class="mt-6 grid gap-4 sm:grid-cols-3">
          <div class="rounded-2xl bg-white/[0.07] p-4 ring-1 ring-white/10">
            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-200/70">Sisa ruang</p>
            <p class="mt-1 text-lg font-extrabold">{{ $summary['remaining'] }}</p>
          </div>
          <div class="rounded-2xl bg-white/[0.07] p-4 ring-1 ring-white/10">
            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-200/70">File aktif</p>
            <p class="mt-1 text-lg font-extrabold">{{ $summary['files'] }} berkas <span class="text-sm font-bold text-emerald-100/70">• {{ $summary['used_active'] }}</span></p>
          </div>
          <div class="rounded-2xl bg-white/[0.07] p-4 ring-1 ring-white/10">
            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-200/70">Di sampah</p>
            <p class="mt-1 text-lg font-extrabold">{{ $summary['trash_bytes'] }} <span class="text-sm font-bold text-emerald-100/70">• {{ $summary['trash_count'] }} berkas</span></p>
          </div>
        </div>
      </div>
    </section>

    <div class="mt-6 grid items-start gap-6 lg:grid-cols-2">
      {{-- ── Per jenis file ── --}}
      <section class="rounded-[28px] glass-card p-6 shadow-lg shadow-slate-900/[0.04] md:p-7">
        <div class="flex items-center gap-3">
          <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
            <x-icon name="archive" class="h-5 w-5" />
          </span>
          <div>
            <h2 class="text-base font-extrabold leading-tight text-slate-900">Berdasarkan Jenis File</h2>
            <p class="text-xs text-slate-500">Pemakaian ruang dari file aktifmu.</p>
          </div>
        </div>

        <div class="mt-6 space-y-5">
          @forelse ($categories as $cat)
            <div>
              <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $cat['bar'] }} text-white shadow-sm">
                  <x-icon :name="$cat['icon']" class="h-4.5 w-4.5" />
                </span>
                <div class="min-w-0 flex-1">
                  <p class="truncate text-sm font-bold text-slate-800">{{ $cat['label'] }}</p>
                  <p class="text-xs text-slate-400">{{ $cat['count'] }} berkas</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-extrabold text-slate-800">{{ $cat['size'] }}</p>
                  <p class="text-xs font-bold text-emerald-600">{{ $cat['percent'] }}%</p>
                </div>
              </div>
              <div class="ml-12 mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-gradient-to-r {{ $cat['bar'] }}" style="width: {{ max(2, $cat['percent']) }}%"></div>
              </div>
            </div>
          @empty
            <p class="py-6 text-center text-sm text-slate-400">Belum ada file yang diunggah.</p>
          @endforelse
        </div>
      </section>

      {{-- ── Per node penyimpanan ── --}}
      <section class="rounded-[28px] glass-card p-6 shadow-lg shadow-slate-900/[0.04] md:p-7">
        <div class="flex items-center gap-3">
          <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
            <x-icon name="cloud" class="h-5 w-5" />
          </span>
          <div>
            <h2 class="text-base font-extrabold leading-tight text-slate-900">Tersimpan di Node</h2>
            <p class="text-xs text-slate-500">Salinan fisik file aktifmu di tiap server.</p>
          </div>
        </div>

        <div class="mt-6 space-y-5">
          @foreach ($nodes as $node)
            <div>
              <div class="flex items-center justify-between gap-3">
                <p class="flex items-center gap-2 text-sm font-bold text-slate-800">
                  <span class="h-2 w-2 rounded-full {{ $node['count'] > 0 ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                  {{ $node['name'] }}
                </p>
                <p class="text-sm font-extrabold text-slate-800">{{ $node['size'] }} <span class="ml-1 text-xs font-bold text-slate-400">{{ $node['count'] }} berkas</span></p>
              </div>
              <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: {{ $node['bytes'] > 0 ? max(2, (int) round($node['bytes'] / $nodeMax * 100)) : 0 }}%"></div>
              </div>
            </div>
          @endforeach
        </div>

        <p class="mt-6 rounded-2xl bg-emerald-50/70 px-4 py-3 text-xs leading-relaxed text-emerald-900/70">
          Setiap file disimpan sebagai salinan utama plus replika di node lain, jadi total ukuran fisik di semua server bisa lebih besar daripada ukuran file aslinya. Kuotamu hanya dihitung dari ukuran file asli.
        </p>
      </section>
    </div>

    {{-- ── File terbesar ── --}}
    <section class="mt-6 rounded-[28px] glass-card p-6 shadow-lg shadow-slate-900/[0.04] md:p-7">
      <div class="flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
          <x-icon name="file" class="h-5 w-5" />
        </span>
        <div>
          <h2 class="text-base font-extrabold leading-tight text-slate-900">File Terbesar</h2>
          <p class="text-xs text-slate-500">Diurutkan dari yang paling banyak memakai ruang.</p>
        </div>
      </div>

      <div class="mt-5 divide-y divide-slate-100">
        @forelse ($largest as $file)
          <div class="flex items-center gap-3 py-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
              <x-icon :name="$file['icon']" class="h-5 w-5" />
            </span>
            <div class="min-w-0 flex-1">
              <p class="truncate text-sm font-bold text-slate-800">{{ $file['name'] }}</p>
              <p class="truncate text-xs text-slate-400">{{ $file['type'] }} • {{ $file['folder'] }} • {{ $file['date'] }}</p>
            </div>
            <span class="shrink-0 text-sm font-extrabold text-slate-800">{{ $file['size'] }}</span>
          </div>
        @empty
          <p class="py-6 text-center text-sm text-slate-400">Belum ada file yang diunggah.</p>
        @endforelse
      </div>
    </section>

    {{-- ── Sampah ── --}}
    @if ($summary['trash_count'] > 0)
      <section class="mt-6 flex flex-wrap items-center gap-4 rounded-[28px] border border-rose-100 bg-rose-50/60 p-6 md:p-7">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-rose-500 shadow-sm">
          <x-icon name="trash" class="h-5 w-5" />
        </span>
        <div class="min-w-0 flex-1">
          <h2 class="text-base font-extrabold text-slate-900">Sampah masih memakai {{ $summary['trash_bytes'] }}</h2>
          <p class="text-xs leading-relaxed text-slate-500">{{ $summary['trash_count'] }} berkas di sampah tetap dihitung ke kuota sampai dihapus permanen. Kosongkan sampah untuk membebaskan ruang.</p>
        </div>
        <a href="{{ route('trash.index') }}" class="inline-flex h-11 shrink-0 items-center rounded-xl bg-gradient-to-r from-rose-500 to-rose-400 px-5 text-sm font-bold text-white shadow-lg shadow-rose-500/25 transition hover:from-rose-400 hover:to-rose-300 active:scale-[0.98]">Buka Sampah</a>
      </section>
    @endif
  </div>
</x-user-layout>
