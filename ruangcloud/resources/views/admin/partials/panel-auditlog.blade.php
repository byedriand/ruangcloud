{{-- Panel Audit Log --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
  <div>
    <h2 class="text-2xl font-extrabold tracking-[-0.035em]">Audit Log</h2>
    <p class="mt-1 text-xs text-[#7b8782]">Rekam jejak semua aktivitas pengguna di sistem.</p>
  </div>
  <div class="flex items-center gap-2">
    <div class="relative">
      <x-icon name="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8d9793]" />
      <input id="audit-search" class="h-9 w-56 rounded-xl bg-white border border-[#e2e9e6] pl-9 pr-3 text-[10px] outline-none focus:ring-2 focus:ring-[#0cb48c]/20"
             placeholder="Cari aktivitas..." oninput="filterAudit(this.value)">
    </div>
    <button type="button" data-action="toast" data-message="Ekspor audit log sedang disiapkan"
            class="flex h-9 items-center gap-2 rounded-xl border border-[#d9e3df] bg-white px-4 text-[10px] font-extrabold text-[#46544f] hover:border-[#0cb48c] transition-colors">
      <x-icon name="download" class="h-4 w-4" /> Ekspor
    </button>
  </div>
</div>

{{-- Activity Summary --}}
<div class="mb-5 grid grid-cols-2 gap-3 md:grid-cols-5">
  @foreach ($auditStats as $stat)
    <div class="rounded-2xl border border-[#e0e8e5] bg-white p-3 text-center">
      <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#9aa39f]">{{ $stat['label'] }}</p>
      <p class="mt-2 text-xl font-extrabold">{{ $stat['count'] }}</p>
    </div>
  @endforeach
</div>

{{-- Filter by Action --}}
<div class="mb-4 flex flex-wrap gap-2">
  <button onclick="filterAuditAction('all')" class="audit-filter active rounded-xl px-3 py-1.5 text-[9px] font-extrabold bg-[#0cb48c] text-white" data-action-filter="all">
    Semua
  </button>
  @foreach (['upload','login','download','delete','share','restore','folder','replicate'] as $act)
    <button onclick="filterAuditAction('{{ $act }}')" class="audit-filter rounded-xl px-3 py-1.5 text-[9px] font-bold bg-[#f0f4f2] text-[#5e6d68] hover:bg-[#e8edeb] transition-colors" data-action-filter="{{ $act }}">
      {{ ucfirst($act) }}
    </button>
  @endforeach
</div>

{{-- Log Table --}}
<article class="overflow-hidden rounded-[20px] border border-[#e0e8e5] bg-white">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[700px]" id="audit-table">
      <thead>
        <tr class="border-b border-[#edf1ef] text-[8px] font-extrabold uppercase tracking-[.1em] text-[#9aa39f]">
          <th class="px-5 py-3 text-left w-10">#</th>
          <th class="px-5 py-3 text-left">Pengguna</th>
          <th class="px-5 py-3 text-left">Aksi</th>
          <th class="px-5 py-3 text-left">Deskripsi</th>
          <th class="px-5 py-3 text-left">Waktu</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($auditLogs as $i => $log)
          @php
            $actionColors = [
              'upload'    => 'bg-[#e9f8f4] text-[#099273]',
              'login'     => 'bg-[#e7f0ff] text-[#4777bc]',
              'download'  => 'bg-[#ede9ff] text-[#6e5ec2]',
              'delete'    => 'bg-[#fff0ee] text-[#c94c3f]',
              'share'     => 'bg-[#fff8e6] text-[#b47e0a]',
              'restore'   => 'bg-[#e9f8f4] text-[#099273]',
              'folder'    => 'bg-[#f5f0ff] text-[#7b5ea7]',
              'replicate' => 'bg-[#e8edff] text-[#4060b0]',
            ];
            $actionColor = $actionColors[$log->action] ?? 'bg-[#f0f2f1] text-[#5e6d68]';
            $actionIcon  = \App\Support\Fmt::actionIcon($log->action);
          @endphp
          <tr class="audit-row border-t border-[#f0f4f2] hover:bg-[#fafcfb] transition-colors"
              data-desc="{{ strtolower($log->description) }}" data-action-type="{{ $log->action }}"
              data-user="{{ strtolower($log->user?->name ?? '') }}">
            <td class="px-5 py-3 text-[9px] text-[#b0bab6]">{{ $auditLogs->firstItem() + $i }}</td>
            <td class="px-5 py-3">
              <div class="flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-[#edf1ef] text-[9px] font-extrabold text-[#5e6d68]">
                  {{ \App\Support\Fmt::initials($log->user?->name ?? 'Sistem') }}
                </div>
                <div>
                  <p class="text-[9px] font-extrabold">{{ $log->user?->name ?? 'Sistem' }}</p>
                  <p class="text-[8px] text-[#9aa39f]">{{ $log->user?->email ?? '—' }}</p>
                </div>
              </div>
            </td>
            <td class="px-5 py-3">
              <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-[8px] font-extrabold {{ $actionColor }}">
                <x-icon :name="$actionIcon" class="h-3 w-3" />
                {{ ucfirst($log->action) }}
              </span>
            </td>
            <td class="px-5 py-3 text-[9px] text-[#4a5450] max-w-[280px]">
              <span class="line-clamp-2">{{ $log->description }}</span>
            </td>
            <td class="px-5 py-3 text-[9px] text-[#7b8782] whitespace-nowrap">
              {{ $log->created_at->locale('id')->translatedFormat('j M Y, H:i') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-12 text-center">
              <div class="flex flex-col items-center gap-2 text-[#b0bab6]">
                <x-icon name="activity" class="h-8 w-8" />
                <p class="text-[10px] font-bold">Belum ada log aktivitas</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if ($auditLogs->hasPages())
    <div class="flex items-center justify-between border-t border-[#edf1ef] px-5 py-3">
      <p class="text-[9px] text-[#7b8782]">
        Menampilkan {{ $auditLogs->firstItem() }}–{{ $auditLogs->lastItem() }} dari {{ $auditLogs->total() }} entri
      </p>
      <div class="flex gap-1">
        @if ($auditLogs->onFirstPage())
          <span class="rounded-lg px-3 py-1.5 text-[9px] text-[#b0bab6]">← Sebelumnya</span>
        @else
          <a href="{{ $auditLogs->previousPageUrl() }}" class="rounded-lg border border-[#e2e9e6] px-3 py-1.5 text-[9px] font-bold hover:border-[#0cb48c] transition-colors">← Sebelumnya</a>
        @endif
        @if ($auditLogs->hasMorePages())
          <a href="{{ $auditLogs->nextPageUrl() }}" class="rounded-lg border border-[#e2e9e6] px-3 py-1.5 text-[9px] font-bold hover:border-[#0cb48c] transition-colors">Berikutnya →</a>
        @else
          <span class="rounded-lg px-3 py-1.5 text-[9px] text-[#b0bab6]">Berikutnya →</span>
        @endif
      </div>
    </div>
  @endif
</article>

<script>
function filterAudit(q) {
  q = q.toLowerCase();
  document.querySelectorAll('#audit-table .audit-row').forEach(row => {
    const match = row.dataset.desc.includes(q) || row.dataset.user.includes(q);
    row.style.display = match ? '' : 'none';
  });
}
function filterAuditAction(action) {
  document.querySelectorAll('.audit-filter').forEach(b => {
    const isActive = b.dataset.actionFilter === action;
    b.classList.toggle('bg-[#0cb48c]', isActive);
    b.classList.toggle('text-white', isActive);
    b.classList.toggle('bg-[#f0f4f2]', !isActive);
    b.classList.toggle('text-[#5e6d68]', !isActive);
  });
  document.querySelectorAll('#audit-table .audit-row').forEach(row => {
    row.style.display = (action === 'all' || row.dataset.actionType === action) ? '' : 'none';
  });
}
</script>
