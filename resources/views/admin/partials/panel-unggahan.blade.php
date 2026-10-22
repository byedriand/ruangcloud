{{-- Panel Unggahan --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
  <div>
    <h2 class="text-2xl font-extrabold tracking-[-0.035em]">Unggahan</h2>
    <p class="mt-1 text-xs text-[#7b8782]">Monitor sesi unggahan aktif dan histori file yang diunggah.</p>
  </div>
</div>

{{-- Stats --}}
<div class="mb-5 grid grid-cols-2 gap-3 md:grid-cols-4">
  <div class="rounded-2xl border border-[#e0e8e5] bg-white p-4">
    <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-[#e9f8f4] text-[#0aa982]">
      <x-icon name="upload" class="h-4 w-4" />
    </div>
    <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#9aa39f]">Sesi Aktif</p>
    <p class="mt-1.5 text-2xl font-extrabold">{{ $uploadStats['active'] }}</p>
  </div>
  <div class="rounded-2xl border border-[#e0e8e5] bg-white p-4">
    <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-[#ede9ff] text-[#6e5ec2]">
      <x-icon name="clock" class="h-4 w-4" />
    </div>
    <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#9aa39f]">Assembling</p>
    <p class="mt-1.5 text-2xl font-extrabold">{{ $uploadStats['assembling'] }}</p>
  </div>
  <div class="rounded-2xl border border-[#e0e8e5] bg-white p-4">
    <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-[#e7f0ff] text-[#4777bc]">
      <x-icon name="file" class="h-4 w-4" />
    </div>
    <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#9aa39f]">Selesai Hari Ini</p>
    <p class="mt-1.5 text-2xl font-extrabold">{{ $uploadStats['completedToday'] }}</p>
  </div>
  <div class="rounded-2xl border border-[#e0e8e5] bg-white p-4">
    <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-[#fff0d9] text-[#b1711a]">
      <x-icon name="archive" class="h-4 w-4" />
    </div>
    <p class="text-[9px] font-bold uppercase tracking-[.1em] text-[#9aa39f]">Total Unggah Hari Ini</p>
    <p class="mt-1.5 text-2xl font-extrabold">{{ $uploadStats['totalSizeToday'] }}</p>
  </div>
</div>

{{-- Active Upload Sessions --}}
<article class="mb-5 overflow-hidden rounded-[20px] border border-[#e0e8e5] bg-white">
  <div class="border-b border-[#e8edeb] px-5 py-4">
    <h3 class="text-sm font-extrabold">Sesi Unggahan Aktif</h3>
    <p class="mt-1 text-[9px] text-[#8b9692]">Proses upload yang sedang berjalan secara real-time</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full min-w-[650px]">
      <thead>
        <tr class="border-b border-[#edf1ef] text-[8px] font-extrabold uppercase tracking-[.1em] text-[#9aa39f]">
          <th class="px-5 py-3 text-left">File</th>
          <th class="px-5 py-3 text-left">Pengguna</th>
          <th class="px-5 py-3 text-left">Ukuran</th>
          <th class="px-5 py-3 text-left">Progress</th>
          <th class="px-5 py-3 text-left">Status</th>
          <th class="px-5 py-3 text-left">Mulai</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($activeSessions as $session)
          @php
            $pct = $session->total_chunks > 0 ? (int)min(100, round($session->received_count / $session->total_chunks * 100)) : 0;
            $statusColor = match($session->status) {
              'uploading'  => 'bg-[#e7f0ff] text-[#4777bc]',
              'assembling' => 'bg-[#fff0d9] text-[#b1711a]',
              default      => 'bg-[#f0f2f1] text-[#7d8985]',
            };
          @endphp
          <tr class="border-t border-[#f0f4f2] hover:bg-[#fafcfb]">
            <td class="px-5 py-3">
              <span class="max-w-[180px] block truncate text-[9px] font-extrabold">{{ $session->file_name }}</span>
            </td>
            <td class="px-5 py-3 text-[9px] text-[#7b8782]">
              {{ $session->user?->name ?? 'Anonim' }}
            </td>
            <td class="px-5 py-3 text-[9px] font-bold">{{ \App\Support\Fmt::bytes($session->file_size) }}</td>
            <td class="px-5 py-3 w-40">
              <div class="flex items-center gap-2">
                <div class="h-1.5 flex-1 rounded-full bg-[#ebefed]">
                  <div class="h-full rounded-full bg-[#0cb48c]" style="width: {{ $pct }}%"></div>
                </div>
                <span class="text-[8px] font-bold text-[#7b8782]">{{ $pct }}%</span>
              </div>
            </td>
            <td class="px-5 py-3">
              <span class="rounded-full px-2 py-1 text-[8px] font-extrabold {{ $statusColor }}">
                {{ ucfirst($session->status) }}
              </span>
            </td>
            <td class="px-5 py-3 text-[9px] text-[#7b8782]">
              {{ $session->created_at->locale('id')->diffForHumans() }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-12 text-center">
              <div class="flex flex-col items-center gap-2 text-[#b0bab6]">
                <x-icon name="upload" class="h-8 w-8" />
                <p class="text-[10px] font-bold">Tidak ada sesi unggahan aktif</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</article>

{{-- Recent Uploaded Files --}}
<article class="overflow-hidden rounded-[20px] border border-[#e0e8e5] bg-white">
  <div class="border-b border-[#e8edeb] px-5 py-4">
    <h3 class="text-sm font-extrabold">File Terbaru Diunggah</h3>
    <p class="mt-1 text-[9px] text-[#8b9692]">20 file terakhir yang berhasil diunggah</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full min-w-[600px]">
      <thead>
        <tr class="border-b border-[#edf1ef] text-[8px] font-extrabold uppercase tracking-[.1em] text-[#9aa39f]">
          <th class="px-5 py-3 text-left">Nama File</th>
          <th class="px-5 py-3 text-left">Pemilik</th>
          <th class="px-5 py-3 text-left">Ukuran</th>
          <th class="px-5 py-3 text-left">Tipe</th>
          <th class="px-5 py-3 text-left">Diunggah</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($recentFiles as $file)
          @php [$type, $icon, $color] = \App\Support\Fmt::meta($file->name, $file->mime); @endphp
          <tr class="border-t border-[#f0f4f2] hover:bg-[#fafcfb]">
            <td class="px-5 py-3">
              <div class="flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#eef7f4] text-[#0aa982]">
                  <x-icon :name="$icon" class="h-3.5 w-3.5" />
                </div>
                <span class="max-w-[180px] block truncate text-[9px] font-extrabold">{{ $file->name }}</span>
              </div>
            </td>
            <td class="px-5 py-3 text-[9px] text-[#7b8782]">{{ $file->user?->name ?? '—' }}</td>
            <td class="px-5 py-3 text-[9px] font-bold">{{ \App\Support\Fmt::bytes($file->size) }}</td>
            <td class="px-5 py-3">
              <span class="rounded-full bg-[#f0f4f2] px-2 py-0.5 text-[8px] font-bold text-[#5e6d68]">{{ $type }}</span>
            </td>
            <td class="px-5 py-3 text-[9px] text-[#7b8782]">
              {{ $file->created_at->locale('id')->diffForHumans() }}
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="py-8 text-center text-[9px] text-[#9aa39f]">Belum ada file.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</article>
