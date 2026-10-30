<x-user-layout title="Aktivitas" active="activity">
  <div class="mb-8">
    <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700">
      <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
      Ruang kerja saya
    </div>
    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Log Aktivitas</h1>
    <p class="mt-1.5 text-base text-slate-600">Catatan riwayat aksi dan aktivitas akun penyimpanan Anda.</p>
  </div>

  @forelse ($items as $item)
    @if ($loop->first)
      <section class="relative">
        <div aria-hidden="true" class="absolute bottom-3 left-[25px] top-3 w-[3px] rounded-full bg-gradient-to-b from-emerald-300/80 via-teal-200/60 to-transparent"></div>
        <div class="space-y-3.5">
    @endif
          <div class="relative flex items-center gap-4">
            <div class="relative z-10 flex h-[52px] w-[52px] shrink-0 items-center justify-center rounded-[18px] bg-gradient-to-br {{ $item['theme'] }} text-white shadow-lg ring-4 ring-white/50">
              <x-icon :name="$item['icon']" class="h-5 w-5" />
            </div>
            <div class="glass-card flex min-w-0 flex-1 items-center gap-4 rounded-[22px] px-5 py-4 transition duration-300 hover:-translate-y-0.5">
              <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-bold text-slate-900">{{ $item['text'] }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ $item['time'] }}</p>
              </div>
              <span class="hidden shrink-0 rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-500/15 sm:block">{{ $item['ago'] }}</span>
            </div>
          </div>
    @if ($loop->last)
        </div>
      </section>
    @endif
  @empty
    <section class="glass-card rounded-[28px]">
      <div class="flex flex-col items-center px-6 py-16 text-center">
        <div class="mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-lg shadow-emerald-500/30">
          <x-icon name="activity" class="h-7 w-7" />
        </div>
        <p class="text-base font-extrabold text-slate-900">Belum ada aktivitas tercatat</p>
        <p class="mt-1 text-sm text-slate-500">Aktivitas akan tercatat otomatis saat Anda mengunggah, mengunduh, atau membagikan file.</p>
      </div>
    </section>
  @endforelse
</x-user-layout>
