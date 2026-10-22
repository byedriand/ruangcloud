@props(['light' => false])
<div class="flex items-center gap-2.5">
  <div class="relative flex h-9 w-10 items-center justify-center text-[#0cb48c]">
    <x-icon name="cloud" class="h-10 w-10" />
    <div class="absolute h-2.5 w-2.5 rounded-full {{ $light ? 'bg-[#b8ffed]' : 'bg-[#0cb48c]' }}"></div>
  </div>
  <span class="text-[21px] font-extrabold tracking-[-0.04em] {{ $light ? 'text-white' : 'text-[#17211e]' }}">ruang<span class="text-[#0cb48c]">cloud</span></span>
</div>
