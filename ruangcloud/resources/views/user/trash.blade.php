<x-user-layout title="Sampah" active="trash">
  <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700">
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
        Ruang kerja saya
      </div>
      <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Tempat Sampah</h1>
      <p class="mt-1.5 text-base text-slate-600">File di sini tetap memakan kuota penyimpanan sampai Anda menghapusnya secara permanen.</p>
    </div>
    @if (count($files) > 0)
      <button type="button" data-action="empty-trash" class="h-11 rounded-xl border border-rose-200 bg-white px-4 text-sm font-bold text-rose-600 shadow-sm transition hover:bg-rose-50">
        Kosongkan semua sampah
      </button>
    @endif
  </div>

  <section class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm">
    @forelse ($files as $file)
      <div class="flex flex-wrap items-center gap-4 border-b border-slate-100 px-6 py-4.5 last:border-0 hover:bg-slate-50/70 transition-colors">
        <div class="file-icon file-{{ $file['color'] }} h-11 w-11 rounded-xl">
          <x-icon :name="$file['icon']" class="h-5 w-5" />
        </div>
        <div class="min-w-0 flex-1 basis-64">
          <p class="truncate text-sm font-bold text-slate-900">{{ $file['name'] }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ $file['size'] }} • dihapus <span class="font-medium text-slate-700">{{ $file['deleted'] }}</span></p>
        </div>
        <button type="button" data-action="restore-file" data-file-id="{{ $file['id'] }}" class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
          <x-icon name="restore" class="h-4 w-4 text-emerald-600" /> Pulihkan
        </button>
        <button type="button" data-action="purge-file" data-file-id="{{ $file['id'] }}" class="h-10 rounded-xl border border-rose-200 bg-white px-3.5 text-xs font-bold text-rose-600 transition hover:bg-rose-50">
          Hapus permanen
        </button>
      </div>
    @empty
      <div class="flex flex-col items-center px-6 py-16 text-center">
        <div class="mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
          <x-icon name="trash" class="h-7 w-7" />
        </div>
        <p class="text-base font-extrabold text-slate-900">Tempat sampah kosong</p>
        <p class="mt-1 text-sm text-slate-500">Berkas yang Anda hapus sementara akan ditampung di sini dan dapat dipulihkan kapan saja.</p>
      </div>
    @endforelse
  </section>
</x-user-layout>
