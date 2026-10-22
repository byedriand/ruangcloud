<x-user-layout title="File saya" active="files" :folder-id="$folder?->id">
  <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div class="min-w-0">
      <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700">
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
        Ruang kerja saya
      </div>
      <h1 class="mt-2 truncate text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">
        @if ($q !== '') Hasil untuk "{{ $q }}" @elseif ($folder) {{ $folder->name }} @else File saya @endif
      </h1>
      <nav class="mt-1.5 flex flex-wrap items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('files.index') }}" class="font-medium hover:text-emerald-600 transition">File saya</a>
        @foreach ($crumbs as $crumb)
          <x-icon name="chevron" class="h-3.5 w-3.5 text-slate-400" />
          <a href="{{ route('files.index', ['folder' => $crumb->id]) }}" class="{{ $loop->last ? 'font-bold text-slate-800' : 'font-medium hover:text-emerald-600' }} transition">{{ $crumb->name }}</a>
        @endforeach
      </nav>
    </div>
    <div class="flex flex-wrap items-center gap-2.5">
      @if ($folder)
        <button type="button" data-action="rename-folder" data-folder-id="{{ $folder->id }}" data-name="{{ $folder->name }}" class="h-11 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 hover:bg-slate-50 transition">Ubah nama</button>
        <button type="button" data-action="delete-folder" data-folder-id="{{ $folder->id }}" data-redirect="{{ route('files.index', $folder->parent_id ? ['folder' => $folder->parent_id] : []) }}" class="h-11 rounded-xl border border-rose-200 bg-white px-4 text-sm font-bold text-rose-600 hover:bg-rose-50 transition">Hapus folder</button>
      @endif
      <button type="button" data-action="add-folder" data-parent-id="{{ $folder?->id }}" class="flex h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"><x-icon name="folder" class="h-4.5 w-4.5 text-slate-500" /> Folder baru</button>
      <button type="button" data-action="open-upload" class="flex h-11 items-center gap-2 rounded-xl bg-emerald-600 px-4.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-500"><x-icon name="upload" class="h-4.5 w-4.5" /> Unggah berkas</button>
    </div>
  </div>

  @if (count($folders) > 0 || $q === '')
    <section class="mb-8">
      <div class="mb-4">
        <h2 class="text-lg font-extrabold tracking-tight text-slate-900 md:text-xl">{{ $folder ? 'Subfolder' : 'Folder' }}</h2>
      </div>
      <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
        @forelse ($folders as $item)
          <x-folder-card :name="$item['name']" :files="$item['files']" :tone="$item['tone']" :href="route('files.index', ['folder' => $item['id']])" />
        @empty
          <div class="col-span-full rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-6 text-center text-sm font-medium text-slate-500">
            Belum ada folder di direktori ini.
          </div>
        @endforelse
      </div>
    </section>
  @endif

  <x-file-table :files="$rows" title="Berkas File" />

  @if ($paginator->hasPages())
    <div class="mt-6">{{ $paginator->links() }}</div>
  @endif
</x-user-layout>
