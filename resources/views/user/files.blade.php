<x-user-layout title="File saya" active="files" :folder-id="$folder?->id">
  <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
    <div class="min-w-0">
      <p class="mb-1 text-xs font-bold uppercase tracking-[0.14em] text-[#0aa982]">Ruang kerja saya</p>
      <h1 class="truncate text-[26px] font-extrabold tracking-[-0.035em] md:text-[30px]">
        @if ($q !== '') Hasil untuk "{{ $q }}" @elseif ($folder) {{ $folder->name }} @else File saya @endif
      </h1>
      <nav class="mt-1 flex flex-wrap items-center gap-1.5 text-sm text-[#78837f]">
        <a href="{{ route('files.index') }}" class="hover:text-[#0aa982]">File saya</a>
        @foreach ($crumbs as $crumb)
          <x-icon name="chevron" class="h-3.5 w-3.5" />
          <a href="{{ route('files.index', ['folder' => $crumb->id]) }}" class="{{ $loop->last ? 'font-bold text-[#27332f]' : 'hover:text-[#0aa982]' }}">{{ $crumb->name }}</a>
        @endforeach
      </nav>
    </div>
    <div class="flex flex-wrap gap-2">
      @if ($folder)
        <button type="button" data-action="rename-folder" data-folder-id="{{ $folder->id }}" data-name="{{ $folder->name }}" class="h-10 rounded-xl border border-[#dce5e1] bg-white px-3.5 text-xs font-bold text-[#44514d] hover:border-[#bfcfc9]">Ubah nama</button>
        <button type="button" data-action="delete-folder" data-folder-id="{{ $folder->id }}" data-redirect="{{ route('files.index', $folder->parent_id ? ['folder' => $folder->parent_id] : []) }}" class="h-10 rounded-xl border border-[#f0d3d1] bg-white px-3.5 text-xs font-bold text-[#d94f4f] hover:bg-[#fff1f0]">Hapus folder</button>
      @endif
      <button type="button" data-action="add-folder" data-parent-id="{{ $folder?->id }}" class="flex h-10 items-center gap-2 rounded-xl border border-[#dce5e1] bg-white px-3.5 text-xs font-bold text-[#44514d] transition hover:border-[#bfcfc9]"><x-icon name="folder" class="h-4 w-4" /> Folder baru</button>
      <button type="button" data-action="open-upload" class="flex h-10 items-center gap-2 rounded-xl bg-[#0cb48c] px-4 text-xs font-bold text-white shadow-[0_6px_18px_rgba(12,180,140,.18)] hover:bg-[#09a47f]"><x-icon name="upload" class="h-4 w-4" /> Unggah file</button>
    </div>
  </div>

  @if (count($folders) > 0 || $q === '')
    <section class="mb-7">
      <div class="mb-3.5"><h2 class="text-base font-extrabold tracking-[-0.02em]">{{ $folder ? 'Subfolder' : 'Folder' }}</h2></div>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
        @forelse ($folders as $item)
          <x-folder-card :name="$item['name']" :files="$item['files']" :tone="$item['tone']" :href="route('files.index', ['folder' => $item['id']])" />
        @empty
          <p class="col-span-full rounded-2xl border border-dashed border-[#d5e2dd] bg-white p-5 text-center text-xs text-[#87918d]">Belum ada folder di sini.</p>
        @endforelse
      </div>
    </section>
  @endif

  <x-file-table :files="$rows" title="File" />

  @if ($paginator->hasPages())
    <div class="mt-5">{{ $paginator->links() }}</div>
  @endif
</x-user-layout>
