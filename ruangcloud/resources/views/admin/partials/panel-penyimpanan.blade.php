{{-- Panel Penyimpanan --}}
<div class="mb-7 flex flex-wrap items-center justify-between gap-4">
  <div>
    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Penyimpanan Terdistribusi</h2>
    <p class="mt-1 text-sm text-slate-500">Pantau kapasitas cluster node penyimpanan dan distribusi file.</p>
  </div>
  <button type="button" data-action="toast" data-message="Laporan penyimpanan sedang disiapkan"
          class="flex h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
    <x-icon name="download" class="h-4 w-4 text-emerald-600" /> Ekspor Laporan
  </button>
</div>

{{-- Overall Usage --}}
<div class="mb-6 rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm">
  <div class="mb-5 flex items-center justify-between">
    <div>
      <h3 class="text-base font-extrabold text-slate-900">Kapasitas Keseluruhan Cluster</h3>
      <p class="mt-0.5 text-xs text-slate-500">Total akumulasi penyimpanan dari seluruh storage node</p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3.5 py-1 text-xs font-bold text-emerald-700">
      {{ $nodesActive }}/{{ $nodesTotal }} Node Beroperasi
    </span>
  </div>
  <div class="mb-4 flex items-end justify-between">
    <div>
      <p class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">{{ $storageUsedFmt }}</p>
      <p class="mt-1 text-xs text-slate-500">dari <span class="font-bold text-slate-700">{{ $storageCapacityFmt }}</span> total kapasitas terdistribusi</p>
    </div>
    <p class="text-3xl font-extrabold text-emerald-600">{{ $storagePercent }}%</p>
  </div>
  <div class="h-3 w-full rounded-full bg-slate-100 overflow-hidden">
    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500"
         style="width: {{ max(2, $storagePercent) }}%"></div>
  </div>
</div>

{{-- Node Cards --}}
<div class="mb-6 grid gap-5 md:grid-cols-3">
  @foreach ($allNodesDetail as $node)
    <article class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm transition hover:shadow-md">
      <div class="mb-4 flex items-center justify-between">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
          <x-icon name="archive" class="h-6 w-6" />
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold {{ $node['status'] === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
          <span class="h-1.5 w-1.5 rounded-full {{ $node['status'] === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
          {{ $node['status'] === 'active' ? 'Aktif' : 'Nonaktif' }}
        </span>
      </div>
      <h4 class="text-base font-extrabold text-slate-900">Node {{ $node['name'] }}</h4>
      <p class="mt-0.5 text-xs text-slate-500">{{ $node['host'] ?? 'localhost' }}</p>

      <div class="mt-5 space-y-2.5">
        <div class="flex justify-between text-xs">
          <span class="text-slate-500">Terpakai</span>
          <span class="font-bold text-slate-800">{{ $node['usedFmt'] }} / {{ $node['capacityFmt'] }}</span>
        </div>
        <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
          <div class="h-full rounded-full transition-all duration-500 {{ $node['usagePercent'] > 80 ? 'bg-amber-500' : 'bg-emerald-500' }}"
               style="width: {{ max(4, $node['usagePercent']) }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-slate-500">
          <span>{{ $node['usagePercent'] }}% terpakai</span>
          <span>{{ $node['freeFmt'] }} tersisa</span>
        </div>
      </div>

      <div class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4">
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">File Tersimpan</p>
          <p class="mt-1 text-base font-extrabold text-slate-900">{{ number_format($node['fileCount']) }}</p>
        </div>
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Salinan Replika</p>
          <p class="mt-1 text-base font-extrabold text-slate-900">{{ number_format($node['replicaCount']) }}</p>
        </div>
      </div>
    </article>
  @endforeach
</div>

{{-- File Distribution --}}
<article class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm">
  <div class="border-b border-slate-100 px-6 py-4.5">
    <h3 class="text-base font-extrabold text-slate-900">Berkas File Terbesar</h3>
    <p class="mt-0.5 text-xs text-slate-500">10 file dengan kapasitas terbesar yang tersimpan di sistem</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full min-w-[650px]">
      <thead>
        <tr class="border-b border-slate-100 bg-slate-50/70 text-xs font-bold uppercase tracking-wider text-slate-500">
          <th class="px-6 py-3.5 text-left">Nama File</th>
          <th class="px-6 py-3.5 text-left">Pemilik</th>
          <th class="px-6 py-3.5 text-left">Ukuran</th>
          <th class="px-6 py-3.5 text-left">Tanggal Unggah</th>
          <th class="px-6 py-3.5 text-left">Replika</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($biggestFiles as $file)
          <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition-colors">
            <td class="px-6 py-3.5">
              <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                  <x-icon name="file" class="h-4.5 w-4.5" />
                </div>
                <span class="max-w-[240px] truncate text-sm font-bold text-slate-900">{{ $file->name }}</span>
              </div>
            </td>
            <td class="px-6 py-3.5 text-xs text-slate-600">{{ $file->user?->name ?? '—' }}</td>
            <td class="px-6 py-3.5">
              <span class="text-xs font-bold text-slate-900">{{\App\Support\Fmt::bytes($file->size)}}</span>
            </td>
            <td class="px-6 py-3.5 text-xs text-slate-500">
              {{ $file->created_at->locale('id')->translatedFormat('j M Y') }}
            </td>
            <td class="px-6 py-3.5">
              <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                {{ $file->replicas->count() }}x
              </span>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="py-10 text-center text-sm text-slate-500">Belum ada file tersimpan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</article>
