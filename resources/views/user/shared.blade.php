<x-user-layout title="Dibagikan" active="shared">
  <div class="mb-7">
    <p class="mb-1 text-xs font-bold uppercase tracking-[0.14em] text-[#0aa982]">Ruang kerja saya</p>
    <h1 class="text-[26px] font-extrabold tracking-[-0.035em] md:text-[30px]">Dibagikan</h1>
    <p class="mt-1 text-sm text-[#78837f]">Kelola tautan berbagi. Buat tautan baru dari menu titik tiga pada file di File saya.</p>
  </div>

  <section class="overflow-hidden rounded-[20px] border border-[#e1e8e5] bg-white">
    @forelse ($shares as $share)
      <div class="flex flex-wrap items-center gap-3.5 border-b border-[#f0f3f2] px-5 py-4 last:border-0">
        <div class="file-icon file-{{ $share['color'] }}"><x-icon :name="$share['icon']" class="h-4.5 w-4.5" /></div>
        <div class="min-w-0 flex-1 basis-56">
          <p class="flex items-center gap-1.5 truncate text-xs font-bold">{{ $share['file'] }} @if ($share['protected'])<x-icon name="lock" class="h-3.5 w-3.5 text-[#8a9591]" />@endif</p>
          <p class="mt-0.5 text-[10px] text-[#929c98]">Berlaku sampai: {{ $share['expires'] }} • {{ $share['downloads'] }}× diunduh</p>
        </div>
        <span class="rounded-full px-2.5 py-1 text-[9px] font-extrabold {{ $share['active'] ? 'bg-[#e9faf5] text-[#098b6c]' : 'bg-[#f0f2f1] text-[#7d8985]' }}">{{ $share['status'] }}</span>
        @if ($share['active'])
          <button type="button" data-action="copy-link" data-url="{{ $share['url'] }}" class="flex h-9 items-center gap-1.5 rounded-xl border border-[#dce5e1] px-3 text-[11px] font-bold text-[#44514d] hover:border-[#bfcfc9]"><x-icon name="copy" class="h-3.5 w-3.5" /> Salin tautan</button>
          <button type="button" data-action="revoke-share" data-share-id="{{ $share['id'] }}" class="h-9 rounded-xl border border-[#f0d3d1] px-3 text-[11px] font-bold text-[#d94f4f] hover:bg-[#fff1f0]">Cabut</button>
        @endif
      </div>
    @empty
      <div class="px-6 py-14 text-center"><div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eff7f4] text-[#0aa982]"><x-icon name="link" class="h-6 w-6" /></div><p class="text-sm font-extrabold">Belum ada tautan berbagi</p><p class="mt-1 text-xs text-[#87928e]">Buka File saya, klik titik tiga pada file, lalu pilih Bagikan.</p><a href="{{ route('files.index') }}" class="mt-4 inline-block rounded-xl bg-[#0cb48c] px-4 py-2.5 text-xs font-bold text-white hover:bg-[#09a47f]">Buka File saya</a></div>
    @endforelse
  </section>
</x-user-layout>
