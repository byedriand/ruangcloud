@props(['name', 'files', 'tone' => 'mint', 'href' => null])
@php
    $tag = $href ? 'a' : 'button';
@endphp
<{{ $tag }} @if ($href) href="{{ $href }}" @else type="button" data-action="toast" data-message="Membuka {{ $name }}" @endif data-folder-name="{{ $name }}" class="group block rounded-2xl border border-slate-200/90 bg-white p-4.5 text-left transition-all hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg hover:shadow-emerald-900/5">
  <div class="mb-3.5 flex items-start justify-between">
    <div class="folder-icon folder-{{ $tone }} h-11 w-11 rounded-xl flex items-center justify-center">
      <x-icon name="folder" class="h-7 w-7" />
    </div>
    <span class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 opacity-40 transition-all group-hover:opacity-100 group-hover:text-emerald-600 group-hover:bg-emerald-50">
      <x-icon name="chevron" class="h-4 w-4" />
    </span>
  </div>
  <p class="truncate text-sm font-bold text-slate-900 transition group-hover:text-emerald-700 md:text-base">{{ $name }}</p>
  <p class="mt-1 text-xs text-slate-500">{{ $files }}</p>
</{{ $tag }}>
