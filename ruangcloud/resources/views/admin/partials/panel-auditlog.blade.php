{{-- Panel Audit Log --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
  <div>
    <h2 class="text-2xl font-extrabold tracking-[-0.035em]">Audit Log</h2>
    <p class="mt-1 text-sm text-[#64748b]">Rekam jejak 10 aktivitas terakhir pengguna di sistem.</p>
  </div>
  <div class="flex items-center gap-2">
    <div class="relative">
      <x-icon name="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8d9793]" />
      <input id="audit-search" class="h-10 w-64 rounded-xl bg-white border border-[#d9e3df] pl-9 pr-3 text-sm outline-none focus:ring-2 focus:ring-[#0cb48c]/20 transition-all"
             placeholder="Cari aktivitas..." oninput="filterAudit(this.value)">
    </div>
    <button type="button" data-action="toast" data-message="Ekspor audit log sedang disiapkan"
            class="flex h-10 items-center gap-2 rounded-xl border border-[#d9e3df] bg-white px-4 text-xs font-bold text-[#46544f] hover:border-[#0cb48c] transition-colors">
      <x-icon name="download" class="h-4 w-4" /> Ekspor
    </button>
  </div>
</div>

{{-- Activity Summary --}}
<div class="mb-5 grid grid-cols-2 gap-3 md:grid-cols-5">
  @foreach ($auditStats as $stat)
    <div class="rounded-2xl border border-[#e0e8e5] bg-white p-4 text-center shadow-xs">
      <p class="text-xs font-bold uppercase tracking-wider text-[#64748b]">{{ $stat['label'] }}</p>
      <p class="mt-2 text-2xl font-black text-[#1a2320]">{{ $stat['count'] }}</p>
    </div>
  @endforeach
</div>


{{-- Log Table --}}
<article class="overflow-hidden rounded-[20px] border border-[#e0e8e5] bg-white shadow-xs">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[700px]" id="audit-table">
      <thead>
        <tr class="border-b border-[#edf1ef] bg-slate-50/75 text-xs font-bold uppercase tracking-wider text-[#64748b]">
          <th class="px-5 py-3.5 text-left w-12">#</th>
          <th class="px-5 py-3.5 text-left">Pengguna</th>
          <th class="px-5 py-3.5 text-left">Aksi</th>
          <th class="px-5 py-3.5 text-left">Deskripsi</th>
          <th class="px-5 py-3.5 text-left">Waktu</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#f0f4f2]">
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
          <tr class="audit-row hover:bg-[#fafcfb] transition-colors"
              data-desc="{{ strtolower($log->description) }}" data-action-type="{{ $log->action }}"
              data-user="{{ strtolower($log->user?->name ?? '') }}">
            <td class="px-5 py-3.5 text-xs font-semibold text-[#8d9793]">{{ $i + 1 }}</td>
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#edf1ef] text-xs font-bold text-[#475569]">
                  {{ \App\Support\Fmt::initials($log->user?->name ?? 'Sistem') }}
                </div>
                <div>
                  <p class="text-sm font-bold text-[#1a2320]">{{ $log->user?->name ?? 'Sistem' }}</p>
                  <p class="text-xs text-[#64748b]">{{ $log->user?->email ?? '—' }}</p>
                </div>
              </div>
            </td>
            <td class="px-5 py-3.5">
              <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold {{ $actionColor }}">
                <x-icon :name="$actionIcon" class="h-3.5 w-3.5" />
                {{ ucfirst($log->action) }}
              </span>
            </td>
            <td class="px-5 py-3.5 text-sm text-[#334155] max-w-[320px]">
              <span class="line-clamp-2">{{ $log->description }}</span>
            </td>
            <td class="px-5 py-3.5 text-xs font-medium text-[#64748b] whitespace-nowrap">
              {{ $log->created_at->locale('id')->translatedFormat('j M Y, H:i') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-12 text-center">
              <div class="flex flex-col items-center gap-2 text-[#b0bab6]">
                <x-icon name="activity" class="h-8 w-8" />
                <p class="text-sm font-bold text-[#64748b]">Belum ada log aktivitas</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Info: hanya 10 aktivitas terakhir --}}
  <div class="flex items-center justify-between border-t border-[#edf1ef] px-5 py-3.5">
    <p class="text-xs font-medium text-[#64748b]">
      Menampilkan {{ $auditLogs->count() }} aktivitas terakhir
    </p>
  </div>
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
