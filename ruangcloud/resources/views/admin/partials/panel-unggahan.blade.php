{{-- Panel Unggahan --}}
<div class="mb-7 flex flex-wrap items-center justify-between gap-4">
  <div>
    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Aktivitas Unggahan</h2>
    <p class="mt-1 text-sm text-slate-500">Monitor sesi unggahan aktif, penggabungan chunk, dan histori file masuk.</p>
  </div>
</div>

{{-- Stats --}}
<div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
  <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm">
    <div class="mb-3.5 flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
      <x-icon name="upload" class="h-5 w-5" />
    </div>
    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Sesi Aktif</p>
    <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-slate-900">{{ $uploadStats['active'] }}</p>
  </div>
  <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm">
    <div class="mb-3.5 flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
      <x-icon name="clock" class="h-5 w-5" />
    </div>
    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Assembling</p>
    <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-slate-900">{{ $uploadStats['assembling'] }}</p>
  </div>
  <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm">
    <div class="mb-3.5 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
      <x-icon name="file" class="h-5 w-5" />
    </div>
    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Selesai Hari Ini</p>
    <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-slate-900">{{ $uploadStats['completedToday'] }}</p>
  </div>
  <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm">
    <div class="mb-3.5 flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
      <x-icon name="archive" class="h-5 w-5" />
    </div>
    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Volume Hari Ini</p>
    <p class="mt-1.5 text-3xl font-extrabold tracking-tight text-slate-900">{{ $uploadStats['totalSizeToday'] }}</p>
  </div>
</div>

{{-- Active Upload Sessions --}}
<article class="mb-6 overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm">
  <div class="border-b border-slate-100 px-6 py-4.5">
    <h3 class="text-base font-extrabold text-slate-900">Sesi Unggahan Berjalan</h3>
    <p class="mt-0.5 text-xs text-slate-500">Proses pengiriman chunk berkas secara real-time</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full min-w-[700px]">
      <thead>
        <tr class="border-b border-slate-100 bg-slate-50/70 text-xs font-bold uppercase tracking-wider text-slate-500">
          <th class="px-6 py-3.5 text-left">Nama File</th>
          <th class="px-6 py-3.5 text-left">Pengguna</th>
          <th class="px-6 py-3.5 text-left">Ukuran</th>
          <th class="px-6 py-3.5 text-left">Progres</th>
          <th class="px-6 py-3.5 text-left">Status</th>
          <th class="px-6 py-3.5 text-left">Mulai</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($activeSessions as $session)
          @php
            $pct = $session->total_chunks > 0 ? (int)min(100, round($session->received_count / $session->total_chunks * 100)) : 0;
            $statusColor = match($session->status) {
              'uploading'  => 'bg-blue-50 text-blue-700',
              'assembling' => 'bg-amber-50 text-amber-700',
              default      => 'bg-slate-100 text-slate-600',
            };
          @endphp
          <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition-colors">
            <td class="px-6 py-3.5">
              <span class="max-w-[220px] block truncate text-sm font-bold text-slate-900">{{ $session->file_name }}</span>
            </td>
            <td class="px-6 py-3.5 text-xs text-slate-600">
              {{ $session->user?->name ?? 'Anonim' }}
            </td>
            <td class="px-6 py-3.5 text-xs font-bold text-slate-800">{{ \App\Support\Fmt::bytes($session->file_size) }}</td>
            <td class="px-6 py-3.5 w-44">
              <div class="flex items-center gap-2.5">
                <div class="h-2 flex-1 rounded-full bg-slate-100 overflow-hidden">
                  <div class="h-full rounded-full bg-emerald-500 transition-all duration-300" style="width: {{ $pct }}%"></div>
                </div>
                <span class="text-xs font-bold text-slate-600">{{ $pct }}%</span>
              </div>
            </td>
            <td class="px-6 py-3.5">
              <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusColor }}">
                {{ ucfirst($session->status) }}
              </span>
            </td>
            <td class="px-6 py-3.5 text-xs text-slate-500">
              {{ $session->created_at->locale('id')->diffForHumans() }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-14 text-center">
              <div class="flex flex-col items-center gap-2 text-slate-400">
                <x-icon name="upload" class="h-8 w-8 text-slate-300" />
                <p class="text-sm font-bold text-slate-600">Tidak ada sesi unggahan yang sedang berjalan</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</article>

{{-- Recent Uploaded Files --}}
<article class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm">
  <div class="border-b border-slate-100 px-6 py-4.5">
    <h3 class="text-base font-extrabold text-slate-900">File Selesai Diunggah Terakhir</h3>
    <p class="mt-0.5 text-xs text-slate-500">20 file terbaru yang berhasil diproses dan disimpan ke node</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full min-w-[650px]">
      <thead>
        <tr class="border-b border-slate-100 bg-slate-50/70 text-xs font-bold uppercase tracking-wider text-slate-500">
          <th class="px-6 py-3.5 text-left">Nama File</th>
          <th class="px-6 py-3.5 text-left">Pemilik</th>
          <th class="px-6 py-3.5 text-left">Ukuran</th>
          <th class="px-6 py-3.5 text-left">Tipe</th>
          <th class="px-6 py-3.5 text-left">Waktu Selesai</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($recentFiles as $file)
          @php [$type, $icon, $color] = \App\Support\Fmt::meta($file->name, $file->mime); @endphp
          <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition-colors">
            <td class="px-6 py-3.5">
              <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                  <x-icon :name="$icon" class="h-4.5 w-4.5" />
                </div>
                <span class="max-w-[220px] block truncate text-sm font-bold text-slate-900">{{ $file->name }}</span>
              </div>
            </td>
            <td class="px-6 py-3.5 text-xs text-slate-600">{{ $file->user?->name ?? '—' }}</td>
            <td class="px-6 py-3.5 text-xs font-bold text-slate-800">{{ \App\Support\Fmt::bytes($file->size) }}</td>
            <td class="px-6 py-3.5">
              <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">{{ $type }}</span>
            </td>
            <td class="px-6 py-3.5 text-xs text-slate-500">
              {{ $file->created_at->locale('id')->diffForHumans() }}
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="py-10 text-center text-sm text-slate-500">Belum ada file yang diunggah.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</article>
