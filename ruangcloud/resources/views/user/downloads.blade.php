<x-user-layout title="Download" active="downloads">
  <div class="mb-8">
    <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700">
      <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
      Ruang kerja saya
    </div>
    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Download File</h1>
    <p class="mt-1.5 text-base text-slate-600">Unduhan yang sedang berjalan tampil di sini dengan progresnya. File yang sudah selesai diunduh tercatat sebagai riwayat dan tidak bisa diunduh lagi.</p>
  </div>

  {{-- Ringkasan --}}
  <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-3">
    <div class="rounded-3xl glass-card p-5 shadow-sm">
      <div class="flex items-center gap-3.5">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-lg shadow-emerald-500/30">
          <x-icon name="check" class="h-5.5 w-5.5" />
        </div>
        <div>
          <p class="text-xl font-extrabold leading-none text-slate-900">{{ $totalCount }}</p>
          <p class="mt-1 text-xs font-semibold text-slate-500">File selesai diunduh</p>
        </div>
      </div>
    </div>
    <div class="rounded-3xl glass-card p-5 shadow-sm">
      <div class="flex items-center gap-3.5">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-lg shadow-emerald-500/30">
          <x-icon name="download" class="h-5.5 w-5.5" />
        </div>
        <div>
          <p class="text-xl font-extrabold leading-none text-slate-900">{{ $totalSize }}</p>
          <p class="mt-1 text-xs font-semibold text-slate-500">Total ukuran unduhan</p>
        </div>
      </div>
    </div>
    <div class="col-span-2 flex items-center rounded-3xl bg-gradient-to-br from-[#0d2f25] via-[#0f3d2e] to-teal-800 p-5 shadow-xl shadow-emerald-950/25 lg:col-span-1">
      <p class="text-sm leading-relaxed text-emerald-50/90">Mulai unduhan dari menu titik tiga file di <b>File saya</b>. File yang sudah <b>Selesai</b> tidak bisa diunduh lagi &mdash; hapus dari riwayat lewat titik tiga bila ingin mengunduh ulang.</p>
    </div>
  </div>

  <section class="overflow-hidden rounded-3xl glass-card shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4.5">
      <div>
        <h2 class="text-base font-extrabold tracking-tight text-slate-900 md:text-lg">Unduhan</h2>
        <p class="mt-0.5 text-xs text-slate-500">Progres unduhan berjalan &amp; riwayat unduhan selesai</p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <div class="min-w-[760px]">
        <div id="dl-table-head" class="grid grid-cols-[36px_minmax(260px,1.8fr)_1fr_110px_150px] items-center gap-4 border-b border-slate-100 bg-slate-50/70 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500">
          <span></span>
          <span>Nama Berkas</span>
          <span>Selesai Diunduh</span>
          <span>Ukuran</span>
          <span class="text-right">Aksi</span>
        </div>
        {{-- Template baris unggahan aktif: dikloning uploader.js per unggahan yang berjalan --}}
        <template id="upload-row-template">
            <div hidden class="grid grid-cols-[36px_minmax(260px,1.8fr)_1fr_110px_150px] items-center gap-4 border-b border-emerald-100/70 bg-emerald-50/40 px-6 py-3.5">
              <span></span>
              <div class="flex min-w-0 items-center gap-3.5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100/80 text-emerald-600">
                  <x-icon name="upload" class="h-5 w-5" />
                </div>
                <div class="min-w-0 flex-1">
                  <p class="ju-name truncate text-sm font-bold text-slate-900">-</p>
                  <div class="mt-2 h-2 overflow-hidden rounded-full bg-emerald-100">
                    <div class="ju-bar upload-progress h-full rounded-full bg-emerald-500 transition-all" style="width: 0%"></div>
                  </div>
                  <p class="ju-meta mt-1.5 text-xs text-slate-500">Menyiapkan unggahan…</p>
                </div>
              </div>
              <span class="ju-status text-sm font-extrabold text-emerald-600">0%</span>
              <span class="ju-size text-sm font-semibold text-slate-700">-</span>
              <div class="flex items-center justify-end gap-2">
                <button type="button" data-action="toggle-upload" class="ju-toggle-label inline-flex h-9.5 items-center rounded-xl bg-emerald-600 px-4 text-xs font-bold text-white shadow-md shadow-emerald-600/20 transition hover:bg-emerald-500 active:scale-[0.98]">Jeda</button>
                <button type="button" data-action="cancel-upload" class="inline-flex h-9.5 items-center rounded-xl border border-rose-200 px-4 text-xs font-bold text-rose-500 transition hover:bg-rose-50">Batal</button>
              </div>
            </div>
          </template>

        <div id="dl-history">
          @forelse ($rows as $row)
            <div class="grid grid-cols-[36px_minmax(260px,1.8fr)_1fr_110px_150px] items-center gap-4 border-b border-slate-100/80 px-6 py-3.5 last:border-0 transition-colors hover:bg-slate-50/70">
              <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <x-icon name="check" class="h-4.5 w-4.5" />
              </span>
              <div class="flex min-w-0 items-center gap-3.5">
                <div class="file-icon file-{{ $row['color'] }} h-10 w-10 shrink-0 rounded-xl">
                  <x-icon :name="$row['icon']" class="h-5 w-5" />
                </div>
                <div class="min-w-0">
                  <p class="truncate text-sm font-bold text-slate-900">{{ $row['name'] }}</p>
                  <p class="mt-0.5 truncate text-xs text-slate-500">{{ $row['type'] }} &bull; {{ $row['folderName'] }}</p>
                </div>
              </div>
              <span class="text-sm text-slate-600">{{ $row['finishedAt'] }}</span>
              <span class="text-sm font-semibold text-slate-700">{{ $row['size'] }}</span>
              <div class="flex items-center justify-end gap-2">
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Selesai</span>
                <div class="menu relative inline-block">
                  <button type="button" data-menu-btn aria-label="Menu unduhan" class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                    <x-icon name="more" class="h-4.5 w-4.5" />
                  </button>
                  <div data-menu-panel hidden class="fixed z-50 min-w-[210px] rounded-2xl glass-card p-2 text-left text-sm font-semibold text-slate-700 shadow-xl shadow-slate-900/10">
                    <a href="{{ route('files.index', $row['folderId'] ? ['folder' => $row['folderId']] : []) }}" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-emerald-50">
                      <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"><x-icon name="folder" class="h-4 w-4" /></span> Buka di File saya
                    </a>
                    <button type="button" data-action="delete-download-record" data-record-id="{{ $row['recordId'] }}" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left font-semibold text-rose-600 transition hover:bg-rose-50">
                      <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-500"><x-icon name="trash" class="h-4 w-4" /></span> Hapus dari riwayat
                    </button>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="flex flex-col items-center px-6 py-16 text-center">
              <div class="mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                <x-icon name="download" class="h-7 w-7" />
              </div>
              <p class="text-base font-extrabold text-slate-800">Belum ada riwayat unduhan</p>
              <p class="mt-1 max-w-md text-sm text-slate-500">Unduh file lewat menu titik tiga di File saya. Selama prosesnya berjalan, progres unduhan tampil di sini; setelah selesai, file tercatat di daftar ini.</p>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </section>
</x-user-layout>
