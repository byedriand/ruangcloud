<x-user-layout title="Aktivitas" active="activity">
  <div class="mb-8">
    <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700">
      <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
      Ruang kerja saya
    </div>
    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Log Aktivitas</h1>
    <p class="mt-1.5 text-base text-slate-600">Catatan riwayat aksi dan aktivitas akun penyimpanan Anda.</p>
  </div>

  <section class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm">
    @forelse ($items as $item)
      <div class="flex items-center gap-4 border-b border-slate-100 px-6 py-4 last:border-0 hover:bg-slate-50/70 transition-colors">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
          <x-icon :name="$item['icon']" class="h-5 w-5" />
        </div>
        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-bold text-slate-900">{{ $item['text'] }}</p>
          <p class="mt-0.5 text-xs text-slate-500">{{ $item['time'] }}</p>
        </div>
        <span class="hidden shrink-0 text-xs font-medium text-slate-500 sm:block">{{ $item['ago'] }}</span>
      </div>
    @empty
      <div class="flex flex-col items-center px-6 py-16 text-center">
        <div class="mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
          <x-icon name="activity" class="h-7 w-7" />
        </div>
        <p class="text-base font-extrabold text-slate-900">Belum ada aktivitas tercatat</p>
        <p class="mt-1 text-sm text-slate-500">Aktivitas akan tercatat otomatis saat Anda mengunggah, mengunduh, atau membagikan file.</p>
      </div>
    @endforelse
  </section>

  @if (isset($paginator) && $paginator->hasPages())
    <div class="mt-6">{{ $paginator->links() }}</div>
  @endif
</x-user-layout>
