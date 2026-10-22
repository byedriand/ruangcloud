<x-user-layout title="Terbaru" active="recent">
  <div class="mb-8">
    <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700">
      <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
      Ruang kerja saya
    </div>
    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Berkas Terbaru</h1>
    <p class="mt-1.5 text-base text-slate-600">50 file yang terakhir Anda unggah dari semua folder.</p>
  </div>

  <x-file-table :files="$rows" title="Berkas yang Baru Diunggah" />
</x-user-layout>
