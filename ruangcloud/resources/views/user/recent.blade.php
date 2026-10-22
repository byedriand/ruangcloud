<x-user-layout title="Terbaru" active="recent">
  <div class="mb-7">
    <p class="mb-1 text-xs font-bold uppercase tracking-[0.14em] text-[#0aa982]">Ruang kerja saya</p>
    <h1 class="text-[26px] font-extrabold tracking-[-0.035em] md:text-[30px]">Terbaru</h1>
    <p class="mt-1 text-sm text-[#78837f]">50 file yang terakhir kamu unggah, dari semua folder.</p>
  </div>

  <x-file-table :files="$rows" title="File terbaru" />
</x-user-layout>
