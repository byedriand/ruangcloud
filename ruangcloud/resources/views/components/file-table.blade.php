@props(['files', 'title' => 'File'])
<section class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm">
  <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4.5">
    <div>
      <h2 class="text-base font-extrabold tracking-tight text-slate-900 md:text-lg">{{ $title }}</h2>
      <p id="file-count" class="mt-0.5 text-xs text-slate-500">{{ count($files) }} file tersimpan</p>
    </div>
    <div class="flex items-center rounded-xl bg-slate-100 p-1">
      <button type="button" aria-label="Tampilan daftar" data-action="set-view" data-view="list" data-view-btn="list" class="rounded-lg bg-white p-2 text-emerald-600 shadow-sm transition">
        <x-icon name="list" class="h-4 w-4" />
      </button>
      <button type="button" aria-label="Tampilan grid" data-action="set-view" data-view="grid" data-view-btn="grid" class="rounded-lg p-2 text-slate-500 transition hover:text-slate-800">
        <x-icon name="grid" class="h-4 w-4" />
      </button>
    </div>
  </div>

  <div id="file-empty" hidden class="flex flex-col items-center px-6 py-16 text-center">
    <div class="mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
      <x-icon name="file" class="h-7 w-7" />
    </div>
    <p class="text-base font-extrabold text-slate-800">Belum ada file di sini</p>
    <p class="mt-1 text-sm text-slate-500">Unggah file baru atau gunakan kata kunci pencarian lain.</p>
  </div>

  {{-- Tampilan List --}}
  <div id="view-list" class="overflow-x-auto">
    <div class="min-w-[700px]">
      <div class="grid grid-cols-[minmax(280px,1.8fr)_1fr_120px_56px] gap-4 border-b border-slate-100 bg-slate-50/70 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500">
        <span>Nama Berkas</span>
        <span>Tanggal Diunggah</span>
        <span>Ukuran</span>
        <span class="text-right">Aksi</span>
      </div>
      @foreach ($files as $file)
        <div data-file-name="{{ mb_strtolower($file['name']) }}" class="group grid grid-cols-[minmax(280px,1.8fr)_1fr_120px_56px] items-center gap-4 border-b border-slate-100/80 px-6 py-3.5 last:border-0 hover:bg-slate-50/70 transition-colors">
          <button type="button"
            data-action="preview-file"
            data-file-id="{{ $file['id'] }}"
            data-name="{{ $file['name'] }}"
            data-mime="{{ $file['mime'] }}"
            data-preview-url="{{ $file['previewUrl'] }}"
            data-download-url="{{ $file['downloadUrl'] }}"
            data-updated="{{ $file['updatedDate'] ?? '' }}"
            class="flex min-w-0 items-center gap-3.5 text-left">
            <div class="file-icon file-{{ $file['color'] }} shrink-0 h-10 w-10 rounded-xl">
              <x-icon :name="$file['icon']" class="h-5 w-5" />
            </div>
            <div class="min-w-0">
              <p class="truncate text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition">{{ $file['name'] }}</p>
              <div class="mt-0.5 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                <span>{{ $file['type'] }}</span>
                @if ($file['updatedDate'])
                  <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                  <span class="font-medium text-emerald-600">Diubah {{ $file['updatedDate'] }}</span>
                @endif
              </div>
            </div>
          </button>
          <div class="min-w-0">
            <span class="block text-sm text-slate-600">{{ $file['date'] }}</span>
            @if ($file['updatedDate'])
              <span class="mt-0.5 block text-xs font-medium text-emerald-600" title="Terakhir diubah: {{ $file['updatedDate'] }}">Diubah {{ $file['updatedDate'] }}</span>
            @endif
          </div>
          <span class="text-sm font-semibold text-slate-700">{{ $file['size'] }}</span>
          <div class="text-right"><x-file-menu :file="$file" /></div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Tampilan Grid --}}
  <div id="view-grid" hidden class="grid grid-cols-2 gap-4 p-5 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
    @foreach ($files as $file)
      <div data-file-name="{{ mb_strtolower($file['name']) }}" class="group relative rounded-2xl border border-slate-200/90 bg-white p-4.5 transition-all hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md hover:shadow-emerald-900/5">
        <div class="absolute right-2 top-2 z-10"><x-file-menu :file="$file" /></div>
        <button type="button"
          data-action="preview-file"
          data-file-id="{{ $file['id'] }}"
          data-name="{{ $file['name'] }}"
          data-mime="{{ $file['mime'] }}"
          data-preview-url="{{ $file['previewUrl'] }}"
          data-download-url="{{ $file['downloadUrl'] }}"
          data-updated="{{ $file['updatedDate'] ?? '' }}"
          class="block w-full text-left">
          <div class="file-icon file-{{ $file['color'] }} mb-4 h-12 w-12 rounded-xl">
            <x-icon :name="$file['icon']" class="h-6 w-6" />
          </div>
          <p class="truncate text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition">{{ $file['name'] }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ $file['size'] }} • {{ $file['type'] }}</p>
          @if ($file['updatedDate'])
            <p class="mt-1 text-xs font-medium text-emerald-600">Diubah {{ $file['updatedDate'] }}</p>
          @endif
        </button>
      </div>
    @endforeach
  </div>
</section>
