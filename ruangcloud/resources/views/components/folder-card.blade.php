@props(['folder', 'href' => null])
<div class="relative" draggable="true" data-drag-folder="{{ $folder['id'] }}" data-drop-folder="{{ $folder['id'] }}">
  <a href="{{ $href }}" class="group block rounded-2xl glass-card p-4.5 text-left transition-all hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg hover:shadow-emerald-900/5">
    <div class="mb-3.5 flex items-start justify-between">
      <div class="folder-icon folder-{{ $folder['tone'] }} h-11 w-11 rounded-xl flex items-center justify-center">
        <x-icon name="folder" class="h-7 w-7" />
      </div>
      <span class="h-9 w-9 shrink-0"></span>
    </div>
    <p class="truncate text-sm font-bold text-slate-900 transition group-hover:text-emerald-700 md:text-base">{{ $folder['name'] }}</p>
    <p class="mt-1 text-xs text-slate-500">{{ $folder['files'] }}</p>
    <div class="mt-3.5 flex items-center gap-2 border-t border-slate-900/5 pt-3">
      @if (! empty($folder['ownerAvatar']))
        <img src="{{ $folder['ownerAvatar'] }}" alt="Foto {{ $folder['owner'] }}" class="h-6 w-6 shrink-0 rounded-lg object-cover ring-1 ring-white">
      @else
        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-teal-600 text-[10px] font-extrabold text-white ring-1 ring-white/40">{{ $folder['ownerInitials'] }}</span>
      @endif
      <span class="truncate text-xs font-semibold text-slate-600">{{ $folder['owner'] }}</span>
      <span class="ml-auto shrink-0 text-[11px] font-medium text-slate-400">{{ $folder['size'] }}</span>
    </div>
  </a>
  <div class="absolute right-3 top-3 z-10">
    <x-folder-menu :folder="$folder" />
  </div>
</div>
