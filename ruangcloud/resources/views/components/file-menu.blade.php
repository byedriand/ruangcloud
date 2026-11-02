@props(['file'])
<div class="menu relative inline-block">
  <button type="button" data-menu-btn aria-label="Menu file" class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
    <x-icon name="more" class="h-4.5 w-4.5" />
  </button>
  <div data-menu-panel hidden class="fixed z-50 min-w-[224px] rounded-2xl glass-card p-2 text-left text-sm font-semibold text-slate-700 shadow-xl shadow-slate-900/10">
    <div class="mb-1 flex items-center gap-3 border-b border-slate-100 px-3 pb-3 pt-2">
      <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-lg shadow-emerald-500/30">
        <x-icon name="file" class="h-4.5 w-4.5" />
      </span>
      <span class="max-w-[150px] truncate text-[13px] font-bold text-slate-900">{{ $file['name'] }}</span>
    </div>
    <button type="button" data-action="preview-file" data-file-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}" data-mime="{{ $file['mime'] }}" data-preview-url="{{ $file['previewUrl'] }}" data-download-url="{{ $file['downloadUrl'] }}" data-updated="{{ $file['updatedDate'] ?? '' }}" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-sky-50">
      <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600"><x-icon name="image" class="h-4 w-4" /></span> Pratinjau
    </button>
    <button type="button" data-action="download-file" data-file-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}" data-size="0" data-mime="{{ $file['mime'] }}" data-url="{{ $file['downloadUrl'] }}" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-emerald-50">
      <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"><x-icon name="download" class="h-4 w-4" /></span> Unduh berkas
    </button>
    <button type="button" data-action="share-file" data-file-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-violet-50">
      <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600"><x-icon name="link" class="h-4 w-4" /></span> Bagikan tautan
    </button>
    <button type="button" data-action="rename-file" data-file-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}" data-base-name="{{ $file['baseName'] ?? '' }}" data-extension="{{ $file['extension'] ?? '' }}" data-type="{{ $file['type'] ?? '' }}" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-amber-50">
      <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600"><x-icon name="file" class="h-4 w-4" /></span> Ubah nama
    </button>
    <button type="button" data-action="move-file" data-file-id="{{ $file['id'] }}" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-teal-50">
      <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-teal-600"><x-icon name="folder" class="h-4 w-4" /></span> Pindahkan
    </button>
    <div class="my-1 border-t border-slate-100"></div>
    <button type="button" data-action="delete-file" data-file-id="{{ $file['id'] }}" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left font-semibold text-rose-600 transition hover:bg-rose-50">
      <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-500"><x-icon name="trash" class="h-4 w-4" /></span> Hapus berkas
    </button>
  </div>
</div>
