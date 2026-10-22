@props(['name', 'files', 'tone' => 'mint', 'href' => null])
@php
    $tag = $href ? 'a' : 'button';
@endphp
<{{ $tag }} @if ($href) href="{{ $href }}" @else type="button" data-action="toast" data-message="Membuka {{ $name }}" @endif data-folder-name="{{ $name }}" class="group block rounded-2xl border border-[#e2e8e5] bg-white p-4 text-left transition hover:-translate-y-0.5 hover:border-[#c9ddd6] hover:shadow-[0_10px_28px_rgba(32,61,52,.07)]">
  <div class="mb-4 flex items-start justify-between">
    <div class="folder-icon folder-{{ $tone }}"><x-icon name="folder" class="h-7 w-7" /></div>
    <x-icon name="chevron" class="h-4 w-4 text-[#98a19e] opacity-40 group-hover:opacity-100" />
  </div>
  <p class="truncate text-xs font-extrabold md:text-sm">{{ $name }}</p>
  <p class="mt-1 text-[10px] text-[#8a9591]">{{ $files }}</p>
</{{ $tag }}>
