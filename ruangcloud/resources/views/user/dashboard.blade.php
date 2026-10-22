<x-user-layout title="Beranda" active="home">
  <div class="mb-8">
    <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700">
      <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
      Ruang kerja saya
    </div>
    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Selamat datang, {{ $firstName }}</h1>
    <p class="mt-1.5 text-base text-slate-600">Kelola semua file pentingmu dalam satu ruang cloud yang cepat dan aman.</p>
  </div>

  <section class="mb-8 grid gap-5 xl:grid-cols-[1fr_340px]">
    {{-- Hero Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0c2f27] via-[#09251f] to-[#051814] p-7 text-white shadow-xl shadow-emerald-950/10 md:p-9">
      <div class="pointer-events-none absolute -right-12 -top-24 h-72 w-72 rounded-full border-[42px] border-emerald-600/20 blur-[1px]"></div>
      <div class="pointer-events-none absolute -bottom-36 right-28 h-64 w-64 rounded-full border-[36px] border-emerald-500/15 blur-[1px]"></div>
      
      <div class="relative z-10 max-w-xl">
        <span class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-bold text-emerald-200 backdrop-blur-md">
          <span class="relative flex h-2 w-2">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
          </span>
          Semua sistem normal & terdistribusi
        </span>
        <h2 class="text-2xl font-extrabold tracking-tight text-white md:text-3xl">File besar? Unggah tanpa khawatir.</h2>
        <p class="mt-3 max-w-lg text-sm leading-relaxed text-emerald-100/85 md:text-base">Teknologi resumable chunk upload menjaga proses tetap aman. Jeda kapan saja dan lanjutkan tanpa mengulang dari awal.</p>
        <button type="button" data-action="open-upload" class="mt-6 inline-flex items-center gap-2.5 rounded-xl bg-emerald-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-900/30 transition-all hover:-translate-y-0.5 hover:bg-emerald-400 active:translate-y-0">
          <x-icon name="upload" class="h-4.5 w-4.5" /> Mulai mengunggah
        </button>
      </div>
    </div>

    {{-- Status Jaringan Card --}}
    <div class="flex flex-col justify-between rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm">
      <div>
        <div class="mb-5 flex items-center justify-between">
          <div>
            <p class="text-base font-extrabold text-slate-900">Status Jaringan</p>
            <p class="mt-0.5 text-xs text-slate-500">Penyimpanan terdistribusi multi-node</p>
          </div>
          <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
            Online
          </span>
        </div>
        
        <div class="space-y-4">
          @foreach ($nodes as $node)
            <div class="flex items-center gap-3.5">
              <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <x-icon name="archive" class="h-5 w-5" />
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-bold text-slate-800">{{ $node['name'] }}</span>
                  <span class="font-bold text-emerald-700">{{ $node['usage'] }}%</span>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                  <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500" style="width: {{ max(4, $node['usage']) }}%"></div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      
      <div class="mt-5 border-t border-slate-100 pt-3 text-xs font-medium text-slate-500">
        Replikasi otomatis 2 salinan fisik aktif
      </div>
    </div>
  </section>

  {{-- Section Folder --}}
  <section class="mb-8">
    <div class="mb-4 flex items-center justify-between">
      <div>
        <h2 class="text-lg font-extrabold tracking-tight text-slate-900 md:text-xl">Folder</h2>
        <p class="mt-0.5 text-sm text-slate-500">Akses cepat koleksi berkas utamamu</p>
      </div>
      <a href="{{ route('files.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-emerald-600 transition hover:text-emerald-700 hover:underline">
        Lihat semua
        <x-icon name="chevron" class="h-3.5 w-3.5" />
      </a>
    </div>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
      @forelse ($folders as $folder)
        <x-folder-card :name="$folder['name']" :files="$folder['files']" :tone="$folder['tone']" :href="route('files.index', ['folder' => $folder['id']])" />
      @empty
        <div class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/60 p-8 text-center">
          <div class="mb-2 flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm">
            <x-icon name="folder" class="h-6 w-6 text-emerald-500" />
          </div>
          <p class="text-sm font-bold text-slate-800">Belum ada folder yang dibuat</p>
          <p class="mt-1 text-xs text-slate-500">Buka menu <a href="{{ route('files.index') }}" class="font-bold text-emerald-600 hover:underline">File saya</a> untuk membuat struktur folder baru.</p>
        </div>
      @endforelse
    </div>
  </section>

  {{-- Section File Terbaru --}}
  <x-file-table :files="$files" title="File terbaru" />
</x-user-layout>
