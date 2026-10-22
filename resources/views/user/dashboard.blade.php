<x-user-layout title="Beranda" active="home">
  <div class="mb-7">
    <p class="mb-1 text-xs font-bold uppercase tracking-[0.14em] text-[#0aa982]">Ruang kerja saya</p>
    <h1 class="text-[26px] font-extrabold tracking-[-0.035em] md:text-[30px]">Selamat datang, {{ $firstName }}</h1>
    <p class="mt-1 text-sm text-[#78837f]">Kelola semua file pentingmu dalam satu ruang yang aman.</p>
  </div>

  <section class="mb-7 grid gap-4 xl:grid-cols-[1fr_310px]">
    <div class="relative overflow-hidden rounded-[22px] bg-[#102f29] px-6 py-6 text-white shadow-[0_16px_40px_rgba(26,48,42,.08)] md:px-8 md:py-7">
      <div class="pointer-events-none absolute -right-10 -top-24 h-64 w-64 rounded-full border-[38px] border-[#1c5348]/50"></div>
      <div class="pointer-events-none absolute -bottom-32 right-32 h-56 w-56 rounded-full border-[32px] border-[#16443b]/70"></div>
      <div class="relative z-10 max-w-xl">
        <span class="mb-4 inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-[10px] font-bold text-[#91e6d1]"><span class="h-1.5 w-1.5 rounded-full bg-[#33d5ad]"></span> Semua sistem normal</span>
        <h2 class="text-xl font-extrabold tracking-[-0.025em] md:text-2xl">File besar? Unggah tanpa khawatir.</h2>
        <p class="mt-2 max-w-lg text-xs leading-5 text-[#acc4be] md:text-sm">Teknologi resumable upload menjaga proses tetap berjalan. Jeda kapan saja dan lanjutkan tanpa mengulang dari awal.</p>
        <button type="button" data-action="open-upload" class="mt-5 flex items-center gap-2 rounded-xl bg-[#0cb48c] px-4 py-2.5 text-xs font-extrabold text-white hover:bg-[#10c99c]"><x-icon name="upload" class="h-4 w-4" /> Mulai mengunggah</button>
      </div>
    </div>

    <div class="rounded-[22px] border border-[#e2e9e6] bg-white p-5">
      <div class="mb-4 flex items-center justify-between"><div><p class="text-sm font-extrabold">Status jaringan</p><p class="mt-0.5 text-[10px] text-[#83908b]">Penyimpanan terdistribusi</p></div></div>
      <div class="space-y-3">
        @foreach ($nodes as $node)
          <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#edf8f4] text-[#0aa982]"><x-icon name="archive" class="h-4 w-4" /></div>
            <div class="min-w-0 flex-1"><div class="flex justify-between text-[10px]"><span class="font-bold">{{ $node['name'] }}</span><span class="text-[#82908b]">{{ $node['usage'] }}%</span></div><div class="mt-1.5 h-1 rounded-full bg-[#edf1ef]"><div class="h-full rounded-full bg-[#0cb48c]" style="width: {{ $node['usage'] }}%"></div></div></div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="mb-7">
    <div class="mb-3.5 flex items-center justify-between"><div><h2 class="text-base font-extrabold tracking-[-0.02em]">Folder</h2><p class="mt-0.5 text-[11px] text-[#87918d]">Akses cepat koleksi utamamu</p></div><a href="{{ route('files.index') }}" class="text-[11px] font-bold text-[#0aa982] hover:underline">Lihat semua</a></div>
    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
      @forelse ($folders as $folder)
        <x-folder-card :name="$folder['name']" :files="$folder['files']" :tone="$folder['tone']" :href="route('files.index', ['folder' => $folder['id']])" />
      @empty
        <p class="col-span-full rounded-2xl border border-dashed border-[#d5e2dd] bg-white p-5 text-center text-xs text-[#87918d]">Belum ada folder. Buka menu <a href="{{ route('files.index') }}" class="font-bold text-[#0aa982] hover:underline">File saya</a> untuk membuat folder.</p>
      @endforelse
    </div>
  </section>

  <x-file-table :files="$files" title="File terbaru" />
</x-user-layout>
