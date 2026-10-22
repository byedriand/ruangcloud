<x-user-layout title="Dibagikan" active="shared">
  <div class="mb-8">
    <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700">
      <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
      Ruang kerja saya
    </div>
    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Tautan Dibagikan</h1>
    <p class="mt-1.5 text-base text-slate-600">Kelola tautan berbagi berkas. Buat tautan baru dari menu titik tiga pada file di halaman File Saya.</p>
  </div>

  <section class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm">
    @forelse ($shares as $share)
      <div class="flex flex-wrap items-center gap-4 border-b border-slate-100 px-6 py-4.5 last:border-0 hover:bg-slate-50/70 transition-colors">
        <div class="file-icon file-{{ $share['color'] }} h-11 w-11 rounded-xl">
          <x-icon :name="$share['icon']" class="h-5 w-5" />
        </div>
        <div class="min-w-0 flex-1 basis-64">
          <p class="flex items-center gap-2 truncate text-sm font-bold text-slate-900">
            {{ $share['file'] }}
            @if ($share['protected'])
              <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">
                <x-icon name="lock" class="h-3 w-3" /> Dilindungi sandi
              </span>
            @endif
          </p>
          <p class="mt-1 text-xs text-slate-500">Berlaku sampai: <span class="font-medium text-slate-700">{{ $share['expires'] }}</span> • Diunduh <span class="font-semibold text-slate-700">{{ $share['downloads'] }}×</span></p>
        </div>
        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $share['active'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
          {{ $share['status'] }}
        </span>
        @if ($share['active'])
          <button type="button" data-action="copy-link" data-url="{{ $share['url'] }}" class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
            <x-icon name="copy" class="h-4 w-4" /> Salin tautan
          </button>
          <button type="button" data-action="revoke-share" data-share-id="{{ $share['id'] }}" class="h-10 rounded-xl border border-rose-200 bg-white px-3.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
            Cabut akses
          </button>
        @endif
      </div>
    @empty
      <div class="flex flex-col items-center px-6 py-16 text-center">
        <div class="mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
          <x-icon name="link" class="h-7 w-7" />
        </div>
        <p class="text-base font-extrabold text-slate-900">Belum ada tautan berbagi aktif</p>
        <p class="mt-1 text-sm text-slate-500">Buka menu File Saya, klik ikon titik tiga pada file, lalu pilih Bagikan.</p>
        <a href="{{ route('files.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-500 transition">
          Buka File Saya
        </a>
      </div>
    @endforelse
  </section>
</x-user-layout>
