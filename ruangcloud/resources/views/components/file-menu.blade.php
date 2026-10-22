@props(['file'])
<details class="menu relative inline-block">
  <summary class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 [&::-webkit-details-marker]:hidden">
    <x-icon name="more" class="h-4.5 w-4.5" />
  </summary>
  <div data-menu-panel class="fixed z-50 min-w-[200px] rounded-2xl border border-slate-200/90 bg-white/95 p-1.5 text-sm font-semibold text-slate-700 shadow-xl shadow-slate-900/10 backdrop-blur-sm">
    <button type="button" data-action="preview-file" data-file-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}" data-mime="{{ $file['mime'] }}" data-preview-url="{{ $file['previewUrl'] }}" data-download-url="{{ $file['downloadUrl'] }}" data-updated="{{ $file['updatedDate'] ?? '' }}" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-left transition hover:bg-slate-100 hover:text-slate-900">
      <x-icon name="image" class="h-4 w-4 text-slate-500" /> Pratinjau
    </button>
    <a href="{{ $file['downloadUrl'] }}" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 transition hover:bg-slate-100 hover:text-slate-900">
      <x-icon name="download" class="h-4 w-4 text-slate-500" /> Unduh berkas
    </a>
    <button type="button" data-action="share-file" data-file-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-left transition hover:bg-slate-100 hover:text-slate-900">
      <x-icon name="link" class="h-4 w-4 text-slate-500" /> Bagikan tautan
    </button>
    <button type="button" data-action="rename-file" data-file-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}" data-base-name="{{ $file['baseName'] ?? '' }}" data-extension="{{ $file['extension'] ?? '' }}" data-type="{{ $file['type'] ?? '' }}" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-left transition hover:bg-slate-100 hover:text-slate-900">
      <x-icon name="file" class="h-4 w-4 text-slate-500" /> Ubah nama
    </button>
    <button type="button" data-action="move-file" data-file-id="{{ $file['id'] }}" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-left transition hover:bg-slate-100 hover:text-slate-900">
      <x-icon name="folder" class="h-4 w-4 text-slate-500" /> Pindahkan
    </button>
    <div class="my-1 border-t border-slate-100"></div>
    <button type="button" data-action="delete-file" data-file-id="{{ $file['id'] }}" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-left font-semibold text-rose-600 transition hover:bg-rose-50">
      <x-icon name="trash" class="h-4 w-4 text-rose-500" /> Hapus berkas
    </button>
  </div>
</details>
