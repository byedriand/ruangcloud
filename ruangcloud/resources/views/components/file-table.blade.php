@props(['files', 'title' => 'File'])
<section class="overflow-hidden rounded-[20px] border border-[#e1e8e5] bg-white">
  <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#e7ecea] px-5 py-4">
    <div><h2 class="text-base font-extrabold tracking-[-0.02em]">{{ $title }}</h2><p id="file-count" class="mt-0.5 text-[10px] text-[#88938f]">{{ count($files) }} file ditampilkan</p></div>
    <div class="flex rounded-lg bg-[#f0f4f2] p-1">
      <button type="button" aria-label="Tampilan daftar" data-action="set-view" data-view="list" data-view-btn="list" class="rounded-md bg-white p-1.5 text-[#0aa982] shadow-sm"><x-icon name="list" class="h-3.5 w-3.5" /></button>
      <button type="button" aria-label="Tampilan grid" data-action="set-view" data-view="grid" data-view-btn="grid" class="rounded-md p-1.5 text-[#85908c]"><x-icon name="grid" class="h-3.5 w-3.5" /></button>
    </div>
  </div>

  <div id="file-empty" hidden class="flex flex-col items-center px-6 py-14 text-center"><div class="mb-3 rounded-2xl bg-[#eff7f4] p-4 text-[#0aa982]"><x-icon name="file" class="h-6 w-6" /></div><p class="text-sm font-extrabold">Belum ada file di sini</p><p class="mt-1 text-xs text-[#87928e]">Unggah file baru atau coba kata kunci lain.</p></div>

  {{-- Tampilan List --}}
  <div id="view-list" class="overflow-x-auto">
    <div class="min-w-[660px]">
      <div class="grid grid-cols-[minmax(250px,1.6fr)_1fr_100px_52px] gap-4 border-b border-[#edf1ef] px-5 py-2.5 text-[9px] font-extrabold uppercase tracking-[0.1em] text-[#9aa39f]"><span>Nama</span><span>Diunggah</span><span>Ukuran</span><span></span></div>
      @foreach ($files as $file)
        <div data-file-name="{{ mb_strtolower($file['name']) }}" class="grid grid-cols-[minmax(250px,1.6fr)_1fr_100px_52px] items-center gap-4 border-b border-[#f0f3f2] px-5 py-3 last:border-0 hover:bg-[#fbfcfc]">
          <button type="button"
            data-action="preview-file"
            data-file-id="{{ $file['id'] }}"
            data-name="{{ $file['name'] }}"
            data-mime="{{ $file['mime'] }}"
            data-preview-url="{{ $file['previewUrl'] }}"
            data-download-url="{{ $file['downloadUrl'] }}"
            data-updated="{{ $file['updatedDate'] ?? '' }}"
            class="flex min-w-0 items-center gap-3 text-left">
            <div class="file-icon file-{{ $file['color'] }} shrink-0"><x-icon :name="$file['icon']" class="h-4.5 w-4.5" /></div>
            <div class="min-w-0">
              <p class="truncate text-xs font-bold">{{ $file['name'] }}</p>
              <div class="mt-0.5 flex flex-wrap items-center gap-1.5 text-[9px] text-[#929c98]">
                <span>{{ $file['type'] }}</span>
                @if ($file['updatedDate'])
                  <span class="h-1 w-1 rounded-full bg-[#b9c6c1]"></span>
                  <span class="font-semibold text-[#0aa982]">Diubah {{ $file['updatedDate'] }}</span>
                @endif
              </div>
            </div>
          </button>
          <div class="min-w-0">
            <span class="block text-[11px] text-[#77827e]">{{ $file['date'] }}</span>
            @if ($file['updatedDate'])
              <span class="mt-0.5 block text-[9px] font-semibold text-[#0aa982]" title="Terakhir diubah: {{ $file['updatedDate'] }}">Diubah {{ $file['updatedDate'] }}</span>
            @endif
          </div>
          <span class="text-[11px] text-[#77827e]">{{ $file['size'] }}</span>
          <div class="text-right"><x-file-menu :file="$file" /></div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Tampilan Grid --}}
  <div id="view-grid" hidden class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3 xl:grid-cols-5">
    @foreach ($files as $file)
      <div data-file-name="{{ mb_strtolower($file['name']) }}" class="relative rounded-xl border border-[#e8ecea] p-4 hover:border-[#cce0d8] hover:bg-[#fbfdfc]">
        <div class="absolute right-1.5 top-1.5"><x-file-menu :file="$file" /></div>
        <button type="button"
          data-action="preview-file"
          data-file-id="{{ $file['id'] }}"
          data-name="{{ $file['name'] }}"
          data-mime="{{ $file['mime'] }}"
          data-preview-url="{{ $file['previewUrl'] }}"
          data-download-url="{{ $file['downloadUrl'] }}"
          data-updated="{{ $file['updatedDate'] ?? '' }}"
          class="block w-full text-left">
          <div class="file-icon file-{{ $file['color'] }} mb-5"><x-icon :name="$file['icon']" class="h-5 w-5" /></div>
          <p class="truncate text-xs font-bold">{{ $file['name'] }}</p>
          <p class="mt-1 text-[9px] text-[#8a9591]">{{ $file['size'] }} • {{ $file['type'] }}</p>
          @if ($file['updatedDate'])
            <p class="mt-1 text-[9px] font-semibold text-[#0aa982]">Diubah {{ $file['updatedDate'] }}</p>
          @endif
        </button>
      </div>
    @endforeach
  </div>
</section>
