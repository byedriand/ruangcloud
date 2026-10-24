<x-user-layout title="Download" active="downloads">
  <div class="mb-8">
    <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700">
      <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
      Ruang kerja saya
    </div>
    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Download File</h1>
    <p class="mt-1.5 text-base text-slate-600">Semua file Anda dari seluruh folder, siap diunduh satu per satu atau beberapa sekaligus.</p>
  </div>

  {{-- Ringkasan --}}
  <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-3">
    <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-sm">
      <div class="flex items-center gap-3.5">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
          <x-icon name="file" class="h-5.5 w-5.5" />
        </div>
        <div>
          <p class="text-xl font-extrabold leading-none text-slate-900">{{ $totalFiles }}</p>
          <p class="mt-1 text-xs font-semibold text-slate-500">File siap diunduh</p>
        </div>
      </div>
    </div>
    <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-sm">
      <div class="flex items-center gap-3.5">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
          <x-icon name="download" class="h-5.5 w-5.5" />
        </div>
        <div>
          <p class="text-xl font-extrabold leading-none text-slate-900">{{ $totalSize }}</p>
          <p class="mt-1 text-xs font-semibold text-slate-500">Total ukuran semua file</p>
        </div>
      </div>
    </div>
    <div class="col-span-2 flex items-center rounded-3xl border border-emerald-100 bg-emerald-50/60 p-5 lg:col-span-1">
      <p class="text-sm leading-relaxed text-emerald-900">Centang beberapa file lalu klik <b>Unduh terpilih</b> untuk mengunduhnya satu demi satu secara otomatis.</p>
    </div>
  </div>

  <section class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4.5">
      <div>
        <h2 class="text-base font-extrabold tracking-tight text-slate-900 md:text-lg">Semua File</h2>
        <p class="mt-0.5 text-xs text-slate-500"><span id="dl-visible-count">{{ count($rows) }}</span> file ditampilkan</p>
      </div>
      <div class="flex flex-wrap items-center gap-2.5">
        <div class="relative">
          <x-icon name="search" class="absolute left-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400" />
          <input id="dl-search" type="text" autocomplete="off" placeholder="Filter nama file..."
                 class="h-10 w-56 rounded-xl border border-slate-200 bg-slate-50/80 pl-10 pr-3.5 text-sm font-medium outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
        </div>
        <button type="button" id="dl-bulk" disabled
                class="flex h-10 items-center gap-2 rounded-xl bg-emerald-600 px-4 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 transition hover:bg-emerald-500 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none">
          <x-icon name="download" class="h-4.5 w-4.5" /> Unduh terpilih (<span id="dl-selected-count">0</span>)
        </button>
      </div>
    </div>

    @if (count($rows) === 0)
      <div class="flex flex-col items-center px-6 py-16 text-center">
        <div class="mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
          <x-icon name="download" class="h-7 w-7" />
        </div>
        <p class="text-base font-extrabold text-slate-800">Belum ada file untuk diunduh</p>
        <p class="mt-1 text-sm text-slate-500">Unggah file pertama Anda, lalu file akan muncul di halaman ini.</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <div class="min-w-[760px]">
          <div id="dl-table-head" class="grid grid-cols-[36px_minmax(260px,1.8fr)_1fr_110px_150px] items-center gap-4 border-b border-slate-100 bg-slate-50/70 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500">
            <input type="checkbox" id="dl-check-all" class="h-4.5 w-4.5 rounded accent-emerald-600" title="Pilih semua">
            <span>Nama Berkas</span>
            <span>Tanggal Diunggah</span>
            <span>Ukuran</span>
            <span class="text-right">Aksi</span>
          </div>
          {{-- Template baris unggahan: diklon oleh uploader.js per job --}}
          <template id="upload-row-template">
            <div class="grid grid-cols-[36px_minmax(260px,1.8fr)_1fr_110px_150px] items-center gap-4 border-b border-emerald-100 bg-emerald-50/60 px-6 py-3.5">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4m0 0 4 4m-4-4L8 8"/><path d="M4 16v2.5A1.5 1.5 0 0 0 5.5 20h13a1.5 1.5 0 0 0 1.5-1.5V16"/></svg>
              </div>
              <div class="min-w-0">
                <p class="ju-name truncate text-sm font-bold text-slate-900">-</p>
                <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-emerald-100">
                  <div class="ju-bar upload-progress h-full rounded-full bg-emerald-500 transition-all" style="width: 0%"></div>
                </div>
                <p class="ju-meta mt-1 text-xs text-slate-500">Menyiapkan unggahan…</p>
              </div>
              <span class="ju-status text-sm font-semibold text-emerald-700">0%</span>
              <span class="ju-size text-sm font-semibold text-slate-700">-</span>
              <div class="flex items-center justify-end gap-2">
                <button type="button" data-action="toggle-upload" class="flex h-9 items-center rounded-xl bg-emerald-600 px-3.5 text-xs font-bold text-white shadow-md shadow-emerald-600/20 transition hover:bg-emerald-500">
                  <span class="ju-toggle-label">Jeda</span>
                </button>
                <button type="button" data-action="cancel-upload" class="flex h-9 items-center rounded-xl border border-rose-200 px-3.5 text-xs font-bold text-rose-600 transition hover:bg-rose-50">
                  Batal
                </button>
              </div>
            </div>
          </template>
          @foreach ($rows as $file)
            <div data-dl-row data-name="{{ mb_strtolower($file['name']) }}" class="grid grid-cols-[36px_minmax(260px,1.8fr)_1fr_110px_150px] items-center gap-4 border-b border-slate-100/80 px-6 py-3.5 last:border-0 transition-colors hover:bg-slate-50/70">
              <input type="checkbox" class="dl-check h-4.5 w-4.5 rounded accent-emerald-600" data-url="{{ $file['downloadUrl'] }}" data-name="{{ $file['name'] }}">
              <div class="flex min-w-0 items-center gap-3.5">
                <div class="file-icon file-{{ $file['color'] }} h-10 w-10 shrink-0 rounded-xl">
                  <x-icon :name="$file['icon']" class="h-5 w-5" />
                </div>
                <div class="min-w-0">
                  <p class="truncate text-sm font-bold text-slate-900">{{ $file['name'] }}</p>
                  <p class="mt-0.5 truncate text-xs text-slate-500">{{ $file['type'] }} • {{ $file['folderName'] }}</p>
                </div>
              </div>
              <span class="text-sm text-slate-600">{{ $file['date'] }}</span>
              <span class="text-sm font-semibold text-slate-700">{{ $file['size'] }}</span>
              <div class="text-right">
                <button type="button"
                        data-action="download-file"
                        data-file-id="{{ $file['id'] }}"
                        data-name="{{ $file['name'] }}"
                        data-size="{{ $file['sizeRaw'] }}"
                        data-mime="{{ $file['mime'] }}"
                        data-url="{{ $file['downloadUrl'] }}"
                        class="inline-flex h-9.5 items-center gap-2 rounded-xl bg-emerald-600 px-4 text-xs font-bold text-white shadow-md shadow-emerald-600/20 transition hover:bg-emerald-500 active:scale-[0.98]">
                  <x-icon name="download" class="h-4 w-4" /> Unduh
                </button>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <div id="dl-filter-empty" hidden class="px-6 py-12 text-center">
        <p class="text-sm font-bold text-slate-700">Tidak ada file yang cocok dengan filter.</p>
        <p class="mt-1 text-xs text-slate-500">Coba kata kunci lain.</p>
      </div>
    @endif
  </section>

  <script>
    (() => {
      const checks = [...document.querySelectorAll('.dl-check')];
      const checkAll = document.getElementById('dl-check-all');
      const bulkBtn = document.getElementById('dl-bulk');
      const countEl = document.getElementById('dl-selected-count');
      const search = document.getElementById('dl-search');
      const visibleEl = document.getElementById('dl-visible-count');
      const filterEmpty = document.getElementById('dl-filter-empty');

      const toast = (msg) => {
        const el = document.getElementById('toast');
        if (!el) return;
        el.textContent = msg;
        el.hidden = false;
        clearTimeout(el._t);
        el._t = setTimeout(() => { el.hidden = true; }, 3200);
      };

      const refresh = () => {
        const selected = checks.filter(c => c.checked);
        countEl.textContent = selected.length;
        bulkBtn.disabled = selected.length === 0;
        if (checkAll) {
          const visible = checks.filter(c => c.closest('[data-dl-row]').style.display !== 'none');
          checkAll.checked = visible.length > 0 && visible.every(c => c.checked);
        }
      };

      checks.forEach(c => c.addEventListener('change', refresh));

      checkAll?.addEventListener('change', () => {
        checks.forEach(c => {
          if (c.closest('[data-dl-row]').style.display !== 'none') c.checked = checkAll.checked;
        });
        refresh();
      });

      bulkBtn?.addEventListener('click', () => {
        const selected = checks.filter(c => c.checked);
        if (!selected.length) return;
        const items = selected.map(c => {
          const btn = c.closest('[data-dl-row]')?.querySelector('[data-action="download-file"]');
          if (!btn) return null;
          return {
            id: Number(btn.dataset.fileId),
            name: btn.dataset.name,
            size: Number(btn.dataset.size || 0),
            mime: btn.dataset.mime || '',
            url: btn.dataset.url,
          };
        }).filter(Boolean);
        if (!items.length) return;
        window.notify?.(`Mengantrekan ${items.length} file untuk diunduh…`);
        window.RuangDownloads?.enqueue(items);
      });

      search?.addEventListener('input', () => {
        const q = search.value.trim().toLowerCase();
        let visible = 0;
        document.querySelectorAll('[data-dl-row]').forEach(row => {
          const show = row.dataset.name.includes(q);
          row.style.display = show ? '' : 'none';
          if (show) visible++;
        });
        visibleEl.textContent = visible;
        if (filterEmpty) filterEmpty.hidden = visible !== 0;
        refresh();
      });

      refresh();
    })();
  </script>
</x-user-layout>
