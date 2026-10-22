<x-user-layout title="Aktivitas" active="activity">
  <div class="mb-7">
    <p class="mb-1 text-xs font-bold uppercase tracking-[0.14em] text-[#0aa982]">Ruang kerja saya</p>
    <h1 class="text-[26px] font-extrabold tracking-[-0.035em] md:text-[30px]">Aktivitas</h1>
    <p class="mt-1 text-sm text-[#78837f]">10 notifikasi aktivitas terakhir pada akunmu.</p>
  </div>

  <section class="overflow-hidden rounded-[20px] border border-[#e1e8e5] bg-white">
    @forelse ($items as $item)
      <div class="flex items-center gap-3.5 border-b border-[#f0f3f2] px-5 py-3.5 last:border-0">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#eef7f4] text-[#0aa982]"><x-icon :name="$item['icon']" class="h-4 w-4" /></div>
        <div class="min-w-0 flex-1"><p class="truncate text-xs font-bold">{{ $item['text'] }}</p><p class="mt-0.5 text-[10px] text-[#929c98]">{{ $item['time'] }}</p></div>
        <span class="hidden shrink-0 text-[10px] text-[#929c98] sm:block">{{ $item['ago'] }}</span>
      </div>
    @empty
      <div class="px-6 py-14 text-center"><div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eff7f4] text-[#0aa982]"><x-icon name="activity" class="h-6 w-6" /></div><p class="text-sm font-extrabold">Belum ada aktivitas</p><p class="mt-1 text-xs text-[#87928e]">Aktivitas akan muncul setelah kamu mengunggah atau mengunduh file.</p></div>
    @endforelse
  </section>

  @if (isset($paginator) && $paginator->hasPages())
    <div class="mt-5">{{ $paginator->links() }}</div>
  @endif
</x-user-layout>
