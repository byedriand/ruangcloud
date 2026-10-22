@props(['file'])
<details class="menu relative inline-block">
  <summary class="cursor-pointer list-none rounded-lg p-2 text-[#87928e] hover:bg-[#edf3f0] [&::-webkit-details-marker]:hidden"><x-icon name="more" class="h-4 w-4" /></summary>
  <div data-menu-panel class="fixed z-50 rounded-xl border border-[#e1e8e5] bg-white p-1 text-xs font-semibold text-[#33423d] shadow-[0_16px_40px_rgba(27,53,45,.16)]">
    <button type="button" data-action="preview-file" data-file-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}" data-mime="{{ $file['mime'] }}" data-preview-url="{{ $file['previewUrl'] }}" data-download-url="{{ $file['downloadUrl'] }}" data-updated="{{ $file['updatedDate'] ?? '' }}" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left hover:bg-[#f3f7f5]"><x-icon name="image" class="h-4 w-4" /> Pratinjau</button>
    <a href="{{ $file['downloadUrl'] }}" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 hover:bg-[#f3f7f5]"><x-icon name="download" class="h-4 w-4" /> Unduh</a>
    <button type="button" data-action="share-file" data-file-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left hover:bg-[#f3f7f5]"><x-icon name="link" class="h-4 w-4" /> Bagikan</button>
    <button type="button" data-action="rename-file" data-file-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}" data-base-name="{{ $file['baseName'] ?? '' }}" data-extension="{{ $file['extension'] ?? '' }}" data-type="{{ $file['type'] ?? '' }}" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left hover:bg-[#f3f7f5]"><x-icon name="file" class="h-4 w-4" /> Ubah nama</button>
    <button type="button" data-action="move-file" data-file-id="{{ $file['id'] }}" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left hover:bg-[#f3f7f5]"><x-icon name="folder" class="h-4 w-4" /> Pindahkan</button>
    <button type="button" data-action="delete-file" data-file-id="{{ $file['id'] }}" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-[#d94f4f] hover:bg-[#fff1f0]"><x-icon name="trash" class="h-4 w-4" /> Pindahkan ke sampah</button>
  </div>
</details>
