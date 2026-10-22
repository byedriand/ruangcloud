<x-user-layout title="Sampah" active="trash">
  <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div>
      <p class="mb-1 text-xs font-bold uppercase tracking-[0.14em] text-[#0aa982]">Ruang kerja saya</p>
      <h1 class="text-[26px] font-extrabold tracking-[-0.035em] md:text-[30px]">Sampah</h1>
      <p class="mt-1 text-sm text-[#78837f]">File di sini masih memakai kuota sampai dihapus permanen.</p>
    </div>
    @if (count($files) > 0)
      <button type="button" data-action="empty-trash" class="h-10 rounded-xl border border-[#f0d3d1] bg-white px-4 text-xs font-bold text-[#d94f4f] hover:bg-[#fff1f0]">Kosongkan sampah</button>
    @endif
  </div>

  <section class="overflow-hidden rounded-[20px] border border-[#e1e8e5] bg-white">
    @forelse ($files as $file)
      <div class="flex flex-wrap items-center gap-3.5 border-b border-[#f0f3f2] px-5 py-3.5 last:border-0">
        <div class="file-icon file-{{ $file['color'] }}"><x-icon :name="$file['icon']" class="h-4.5 w-4.5" /></div>
        <div class="min-w-0 flex-1 basis-56"><p class="truncate text-xs font-bold">{{ $file['name'] }}</p><p class="mt-0.5 text-[10px] text-[#929c98]">{{ $file['size'] }} • dihapus {{ $file['deleted'] }}</p></div>
        <button type="button" data-action="restore-file" data-file-id="{{ $file['id'] }}" class="flex h-9 items-center gap-1.5 rounded-xl border border-[#dce5e1] px-3 text-[11px] font-bold text-[#44514d] hover:border-[#bfcfc9]"><x-icon name="restore" class="h-3.5 w-3.5" /> Pulihkan</button>
        <button type="button" data-action="purge-file" data-file-id="{{ $file['id'] }}" class="h-9 rounded-xl border border-[#f0d3d1] px-3 text-[11px] font-bold text-[#d94f4f] hover:bg-[#fff1f0]">Hapus permanen</button>
      </div>
    @empty
      <div class="px-6 py-14 text-center"><div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eff7f4] text-[#0aa982]"><x-icon name="trash" class="h-6 w-6" /></div><p class="text-sm font-extrabold">Sampah kosong</p><p class="mt-1 text-xs text-[#87928e]">File yang kamu hapus akan muncul di sini dan bisa dipulihkan.</p></div>
    @endforelse
  </section>
</x-user-layout>
