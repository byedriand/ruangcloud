{{-- Panel Penyimpanan --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
  <div>
    <h2 class="text-2xl font-extrabold tracking-[-0.035em]">Penyimpanan</h2>
    <p class="mt-1 text-xs text-[#7b8782]">Pantau kapasitas storage node dan distribusi file.</p>
  </div>
  <button type="button" data-action="toast" data-message="Laporan penyimpanan sedang disiapkan"
          class="flex h-10 items-center gap-2 rounded-xl border border-[#d9e3df] bg-white px-4 text-[10px] font-extrabold text-[#46544f] hover:border-[#0cb48c] transition-colors">
    <x-icon name="download" class="h-4 w-4" /> Ekspor Laporan
  </button>
</div>

{{-- Overall Usage --}}
<div class="mb-5 rounded-[20px] border border-[#e0e8e5] bg-white p-5">
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h3 class="text-sm font-extrabold">Kapasitas Keseluruhan</h3>
      <p class="mt-1 text-[9px] text-[#8b9692]">Total kapasitas dari semua storage node</p>
    </div>
    <span class="rounded-full bg-[#e8faf5] px-3 py-1 text-[9px] font-extrabold text-[#099273]">
      {{ $nodesActive }}/{{ $nodesTotal }} Node Aktif
    </span>
  </div>
  <div class="mb-3 flex items-end justify-between">
    <div>
      <p class="text-3xl font-extrabold tracking-tight">{{ $storageUsedFmt }}</p>
      <p class="mt-1 text-[10px] text-[#7b8782]">dari {{ $storageCapacityFmt }} total kapasitas</p>
    </div>
    <p class="text-2xl font-extrabold text-[#0cb48c]">{{ $storagePercent }}%</p>
  </div>
  <div class="h-3 w-full rounded-full bg-[#edf1ef]">
    <div class="h-full rounded-full bg-gradient-to-r from-[#0cb48c] to-[#3dd9b3] transition-all"
         style="width: {{ $storagePercent }}%"></div>
  </div>
</div>

{{-- Node Cards --}}
<div class="mb-5 grid gap-4 md:grid-cols-3">
  @foreach ($allNodesDetail as $node)
    <article class="rounded-[20px] border border-[#e0e8e5] bg-white p-5">
      <div class="mb-4 flex items-center justify-between">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#e9f8f4] text-[#0aa982]">
          <x-icon name="archive" class="h-5 w-5" />
        </div>
        <span class="flex items-center gap-1.5 text-[9px] font-extrabold {{ $node['status'] === 'active' ? 'text-[#099273]' : 'text-[#e05252]' }}">
          <span class="h-1.5 w-1.5 rounded-full {{ $node['status'] === 'active' ? 'bg-[#29cfa5]' : 'bg-[#ff776e]' }}"></span>
          {{ $node['status'] === 'active' ? 'Aktif' : 'Nonaktif' }}
        </span>
      </div>
      <h4 class="text-sm font-extrabold">Node {{ $node['name'] }}</h4>
      <p class="mt-0.5 text-[9px] text-[#8b9692]">{{ $node['host'] ?? 'localhost' }}</p>

      <div class="mt-4 space-y-2">
        <div class="flex justify-between text-[9px]">
          <span class="text-[#7a8782]">Terpakai</span>
          <span class="font-extrabold">{{ $node['usedFmt'] }} / {{ $node['capacityFmt'] }}</span>
        </div>
        <div class="h-2 w-full rounded-full bg-[#edf1ef]">
          <div class="h-full rounded-full {{ $node['usagePercent'] > 80 ? 'bg-[#f0a545]' : 'bg-[#0cb48c]' }}"
               style="width: {{ $node['usagePercent'] }}%"></div>
        </div>
        <div class="flex justify-between text-[9px] text-[#8b9692]">
          <span>{{ $node['usagePercent'] }}% terpakai</span>
          <span>{{ $node['freeFmt'] }} tersisa</span>
        </div>
      </div>

      <div class="mt-4 grid grid-cols-2 gap-2 border-t border-[#f0f4f2] pt-4">
        <div>
          <p class="text-[8px] text-[#9aa39f]">File Tersimpan</p>
          <p class="mt-0.5 text-sm font-extrabold">{{ number_format($node['fileCount']) }}</p>
        </div>
        <div>
          <p class="text-[8px] text-[#9aa39f]">Replika</p>
          <p class="mt-0.5 text-sm font-extrabold">{{ number_format($node['replicaCount']) }}</p>
        </div>
      </div>
    </article>
  @endforeach
</div>

{{-- File Distribution --}}
<article class="rounded-[20px] border border-[#e0e8e5] bg-white p-5">
  <div class="mb-5">
    <h3 class="text-sm font-extrabold">File Terbesar</h3>
    <p class="mt-1 text-[9px] text-[#8b9692]">10 file terbesar yang tersimpan di sistem</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full min-w-[600px]">
      <thead>
        <tr class="border-b border-[#edf1ef] text-[8px] font-extrabold uppercase tracking-[.1em] text-[#9aa39f]">
          <th class="pb-2 text-left">Nama File</th>
          <th class="pb-2 text-left">Pemilik</th>
          <th class="pb-2 text-left">Ukuran</th>
          <th class="pb-2 text-left">Tanggal Unggah</th>
          <th class="pb-2 text-left">Replika</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($biggestFiles as $file)
          <tr class="border-t border-[#f0f4f2] hover:bg-[#fafcfb]">
            <td class="py-3 pr-4">
              <div class="flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#eef7f4] text-[#0aa982]">
                  <x-icon name="file" class="h-3.5 w-3.5" />
                </div>
                <span class="max-w-[200px] truncate text-[9px] font-extrabold">{{ $file->name }}</span>
              </div>
            </td>
            <td class="py-3 pr-4 text-[9px] text-[#7b8782]">{{ $file->user?->name ?? '—' }}</td>
            <td class="py-3 pr-4">
              <span class="text-[9px] font-extrabold text-[#18231f]">{{\App\Support\Fmt::bytes($file->size)}}</span>
            </td>
            <td class="py-3 pr-4 text-[9px] text-[#7b8782]">
              {{ $file->created_at->locale('id')->translatedFormat('j M Y') }}
            </td>
            <td class="py-3">
              <span class="rounded-full bg-[#e9faf5] px-2 py-0.5 text-[8px] font-extrabold text-[#099273]">
                {{ $file->replicas->count() }}x
              </span>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="py-8 text-center text-[9px] text-[#9aa39f]">Belum ada file tersimpan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</article>
