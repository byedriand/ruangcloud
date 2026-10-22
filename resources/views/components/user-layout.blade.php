<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title }} — RuangCloud</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-folder-id="{{ $folderId }}">
<div class="min-h-screen bg-[#f7f9f8] text-[#17211e]">
  <button type="button" id="sidebar-backdrop" hidden aria-label="Tutup menu" data-action="close-sidebar" class="fixed inset-0 z-30 bg-black/25 lg:hidden"></button>

  <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-[248px] -translate-x-full flex-col border-r border-[#e4eae7] bg-white px-4 pb-5 pt-6 transition-transform lg:translate-x-0">
    <div class="mb-8 flex items-center gap-2.5 px-2">
      <a href="{{ route('dashboard') }}"><x-brand /></a>
      <button type="button" data-action="close-sidebar" class="ml-auto lg:hidden"><x-icon name="x" /></button>
    </div>

    <button type="button" data-action="open-upload" class="mb-6 flex h-11 items-center justify-center gap-2 rounded-xl bg-[#0cb48c] text-sm font-bold text-white shadow-[0_8px_22px_rgba(12,180,140,.22)] transition hover:bg-[#099f7b]">
      <x-icon name="plus" class="h-4.5 w-4.5" /> Unggah baru
    </button>

    <nav class="space-y-1">
      @foreach ($nav as $item)
        <a href="{{ route($item['route']) }}"
           class="flex h-10 w-full items-center gap-3 rounded-xl px-3 text-sm font-semibold transition {{ $active === $item['key'] ? 'bg-[#eafaf6] text-[#079a77]' : 'text-[#6d7774] hover:bg-[#f3f7f5] hover:text-[#27332f]' }}">
          <x-icon :name="$item['icon']" class="h-[19px] w-[19px]" />
          {{ $item['label'] }}
          @if ($item['badge'])
            <span class="ml-auto rounded-full bg-[#eef2f0] px-2 py-0.5 text-[10px] text-[#72807b]">{{ $item['badge'] }}</span>
          @endif
        </a>
      @endforeach
    </nav>

    <div class="mt-auto rounded-2xl border border-[#e4ebe8] bg-[#f9fbfa] p-3.5">
      <div class="mb-2.5 flex items-center justify-between text-xs">
        <span class="font-bold text-[#36443f]">Penyimpanan</span>
        <span class="text-[#84908c]">{{ $storage['percent'] }}%</span>
      </div>
      <div class="h-1.5 overflow-hidden rounded-full bg-[#dfe8e4]"><div class="h-full rounded-full {{ $storage['percent'] > 85 ? 'bg-[#f0a545]' : 'bg-[#0cb48c]' }}" style="width: {{ $storage['percent'] }}%"></div></div>
      <p class="mt-2.5 text-[11px] text-[#818c88]"><b class="text-[#4d5a56]">{{ $storage['used'] }}</b> dari {{ $storage['total'] }} terpakai</p>
      @if ($storage['percent'] > 85)
        <p class="mt-2 text-[10px] font-bold text-[#c27a14]">Penyimpanan hampir penuh. Hapus file yang tidak dipakai.</p>
      @endif
    </div>

    <button type="button" data-action="open-logout" title="Keluar dari akun" class="group mt-4 flex w-full items-center gap-3 rounded-xl px-2 py-2 text-left transition hover:bg-[#fff5f5]">
      <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#d9f4ed] text-xs font-extrabold text-[#098568]">{{ $user['initials'] }}</div>
      <div class="min-w-0"><p class="truncate text-xs font-bold">{{ $user['name'] }}</p><p class="truncate text-[10px] text-[#84908c]">{{ $user['email'] }}</p></div>
      <span class="ml-auto rounded-lg bg-[#fff1f0] px-2.5 py-1 text-[10px] font-bold text-[#e05252] transition group-hover:bg-[#ffe2e0]">Keluar</span>
    </button>
  </aside>

  <div class="lg:pl-[248px]">
    <header class="sticky top-0 z-20 flex h-[72px] items-center gap-3 border-b border-[#e5eae8] bg-white/95 px-4 backdrop-blur md:px-7">
      <button type="button" aria-label="Buka menu" data-action="open-sidebar" class="rounded-lg p-2 text-[#51605a] hover:bg-[#f0f5f3] lg:hidden"><x-icon name="menu" /></button>
      <form method="GET" action="{{ route('files.index') }}" class="relative max-w-[460px] flex-1">
        <x-icon name="search" class="absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[#89938f]" />
        <input id="search" name="q" value="{{ request('q') }}" type="text" autocomplete="off" class="h-10 w-full rounded-xl border border-[#e0e7e4] bg-[#f8faf9] pl-10 pr-4 text-sm outline-none transition placeholder:text-[#9aa39f] focus:border-[#0cb48c] focus:bg-white focus:ring-3 focus:ring-[#0cb48c]/10" placeholder="Cari file atau folder, tekan Enter...">
      </form>
      <div class="ml-auto flex items-center gap-2">
        <a href="{{ route('activity.index') }}" aria-label="Aktivitas" class="relative rounded-xl p-2.5 text-[#56635f] hover:bg-[#f2f6f4]"><x-icon name="bell" class="h-5 w-5" /></a>
        <div class="hidden h-8 w-px bg-[#e5e9e7] sm:block"></div>
        <div class="hidden h-8 w-8 items-center justify-center rounded-full bg-[#d9f4ed] text-[10px] font-extrabold text-[#098568] sm:flex">{{ $user['initials'] }}</div>
      </div>
    </header>

    <main class="mx-auto max-w-[1440px] px-4 py-6 md:px-7 md:py-8">
      {{ $slot }}
    </main>
  </div>

  {{-- Kartu progres unggahan (diisi uploader.js) --}}
  <div id="upload-card" hidden class="fixed bottom-5 right-5 z-30 w-[300px] overflow-hidden rounded-2xl border border-[#dce6e2] bg-white shadow-[0_16px_50px_rgba(27,53,45,.16)] sm:right-7">
    <div class="flex items-center gap-3 p-3.5">
      <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#e8faf5] text-[#0aa982]"><x-icon name="upload" class="h-4 w-4" /></div>
      <div class="min-w-0 flex-1">
        <div class="flex justify-between"><p id="up-name" class="truncate text-[11px] font-extrabold">-</p><span id="up-status" class="text-[10px] font-bold text-[#0aa982]">0%</span></div>
        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#e9efec]"><div id="up-bar" class="upload-progress h-full rounded-full bg-[#0cb48c]" style="width: 0%"></div></div>
        <p id="up-meta" class="mt-1.5 text-[9px] text-[#89948f]"></p>
      </div>
      <button type="button" id="up-toggle" data-action="toggle-upload" aria-label="Jeda unggahan" class="rounded-lg p-2 text-[#5f6d68] hover:bg-[#f0f5f3]">
        <span id="icon-pause"><x-icon name="pause" class="h-4 w-4" /></span>
        <span id="icon-resume" hidden><x-icon name="upload" class="h-4 w-4" /></span>
      </button>
      <button type="button" data-action="cancel-upload" aria-label="Batalkan unggahan" class="rounded-lg p-2 text-[#5f6d68] hover:bg-[#f0f5f3]"><x-icon name="x" class="h-4 w-4" /></button>
    </div>
  </div>

  {{-- Modal upload --}}
  <div id="upload-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-[#10251f]/45 p-4 backdrop-blur-[2px]">
    <div class="w-full max-w-[480px] rounded-[24px] bg-white p-5 shadow-[0_30px_80px_rgba(12,39,31,.22)] md:p-6">
      <div class="mb-5 flex items-start justify-between"><div><h2 class="text-xl font-extrabold tracking-[-0.03em]">Unggah file</h2><p class="mt-1 text-xs text-[#7c8883]">File besar akan diunggah dalam beberapa bagian.</p></div><button type="button" data-action="close-upload" class="rounded-xl p-2 text-[#7d8985] hover:bg-[#f2f6f4]"><x-icon name="x" class="h-5 w-5" /></button></div>
      <label id="drop-zone" class="flex cursor-pointer flex-col items-center rounded-2xl border-2 border-dashed border-[#b9ddd3] bg-[#f6fcfa] px-6 py-9 text-center transition hover:border-[#0cb48c] hover:bg-[#f0fbf8]">
        <input id="upload-input" type="file" class="hidden">
        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#dcf6ef] text-[#0aa982]"><x-icon name="upload" class="h-6 w-6" /></div>
        <p class="text-sm font-extrabold">Tarik file ke sini</p><p class="mt-1 text-xs text-[#85918c]">atau <span class="font-bold text-[#0aa982]">pilih dari perangkat</span></p>
        <span class="mt-4 rounded-full bg-white px-3 py-1 text-[9px] font-semibold text-[#84908c] shadow-sm">Maksimum 5 GB per file</span>
      </label>
      <div class="mt-4 flex items-center gap-2 rounded-xl bg-[#f4f7f6] p-3 text-[10px] text-[#71807a]"><x-icon name="cloud" class="h-4 w-4 shrink-0 text-[#0aa982]" /><p>Unggahan aman dan dapat dilanjutkan jika koneksi terputus.</p></div>
    </div>
  </div>

  {{-- Modal bagikan --}}
  <div id="share-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-[#10251f]/45 p-4 backdrop-blur-[2px]">
    <div class="w-full max-w-[440px] rounded-[24px] bg-white p-5 shadow-[0_30px_80px_rgba(12,39,31,.22)] md:p-6">
      <div class="mb-5 flex items-start justify-between"><div class="min-w-0"><h2 class="text-xl font-extrabold tracking-[-0.03em]">Bagikan file</h2><p id="share-file-name" class="mt-1 truncate text-xs text-[#7c8883]"></p></div><button type="button" data-action="close-dialog" data-dialog="share-modal" class="rounded-xl p-2 text-[#7d8985] hover:bg-[#f2f6f4]"><x-icon name="x" class="h-5 w-5" /></button></div>
      <div id="share-form" class="space-y-4">
        <label class="block"><span class="mb-2 block text-[11px] font-bold text-[#40504a]">Kata sandi (opsional)</span><input id="share-password" type="text" autocomplete="off" placeholder="Kosongkan bila tanpa kata sandi" class="h-11 w-full rounded-xl border border-[#dbe5e1] px-4 text-sm outline-none focus:border-[#0cb48c]"></label>
        <label class="block"><span class="mb-2 block text-[11px] font-bold text-[#40504a]">Masa berlaku</span>
          <select id="share-expiry" class="h-11 w-full rounded-xl border border-[#dbe5e1] bg-white px-3 text-sm outline-none focus:border-[#0cb48c]"><option value="">Tanpa batas</option><option value="1">1 hari</option><option value="7" selected>7 hari</option><option value="30">30 hari</option></select>
        </label>
        <button type="button" data-action="create-share" class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white hover:bg-[#09a37e]"><x-icon name="link" class="h-4 w-4" /> Buat tautan</button>
      </div>
      <div id="share-result" hidden class="space-y-3">
        <p class="text-xs font-bold text-[#0a8f70]">Tautan siap dibagikan</p>
        <div class="flex gap-2"><input id="share-url" readonly class="h-11 min-w-0 flex-1 rounded-xl border border-[#dbe5e1] bg-[#f8faf9] px-3 text-xs outline-none"><button type="button" data-action="copy-share" class="flex h-11 items-center gap-1.5 rounded-xl bg-[#0cb48c] px-4 text-xs font-extrabold text-white"><x-icon name="copy" class="h-4 w-4" /> Salin</button></div>
        <p class="text-[10px] text-[#8a9591]">Kamu bisa mencabut tautan kapan saja di halaman Dibagikan.</p>
      </div>
    </div>
  </div>

  {{-- Modal pindahkan --}}
  <div id="move-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-[#10251f]/45 p-4 backdrop-blur-[2px]">
    <div class="w-full max-w-[400px] rounded-[24px] bg-white p-5 shadow-[0_30px_80px_rgba(12,39,31,.22)] md:p-6">
      <div class="mb-5 flex items-start justify-between"><h2 class="text-xl font-extrabold tracking-[-0.03em]">Pindahkan file</h2><button type="button" data-action="close-dialog" data-dialog="move-modal" class="rounded-xl p-2 text-[#7d8985] hover:bg-[#f2f6f4]"><x-icon name="x" class="h-5 w-5" /></button></div>
      <label class="block"><span class="mb-2 block text-[11px] font-bold text-[#40504a]">Folder tujuan</span>
        <select id="move-folder" class="h-11 w-full rounded-xl border border-[#dbe5e1] bg-white px-3 text-sm outline-none focus:border-[#0cb48c]">
          <option value="">File saya (folder utama)</option>
          @foreach ($folders as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach
        </select>
      </label>
      <button type="button" data-action="confirm-move" class="mt-5 flex h-11 w-full items-center justify-center rounded-xl bg-[#0cb48c] text-sm font-extrabold text-white hover:bg-[#09a37e]">Pindahkan</button>
    </div>
  </div>

  {{-- Modal ubah nama file --}}
  <div id="rename-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-[#10251f]/45 p-4 backdrop-blur-[2px]">
    <div class="w-full max-w-[430px] rounded-[24px] bg-white p-5 shadow-[0_30px_80px_rgba(12,39,31,.22)] md:p-6">
      <div class="mb-4 flex items-start justify-between">
        <div>
          <h2 class="text-xl font-extrabold tracking-[-0.03em]">Ubah nama file</h2>
          <p class="mt-1 text-xs text-[#7c8883]">Ekstensi file otomatis dipertahankan.</p>
        </div>
        <button type="button" data-action="close-dialog" data-dialog="rename-modal" class="rounded-xl p-2 text-[#7d8985] hover:bg-[#f2f6f4]"><x-icon name="x" class="h-5 w-5" /></button>
      </div>

      <div class="mb-4 flex items-center justify-between rounded-xl border border-[#e5ebe8] bg-[#f8faf9] px-3.5 py-2.5">
        <span class="text-xs font-semibold text-[#66736e]">Keterangan tipe:</span>
        <span id="rename-file-type" class="rounded-lg bg-white px-2.5 py-1 text-xs font-extrabold text-[#0aa982] shadow-sm">Foto PNG</span>
      </div>

      <form id="rename-form" onsubmit="event.preventDefault(); window.confirmRenameFile?.();">
        <label class="block">
          <span class="mb-2 block text-[11px] font-bold text-[#40504a]">Nama file baru</span>
          <div class="relative flex items-center">
            <input id="rename-file-input" type="text" autocomplete="off" class="h-11 w-full rounded-xl border border-[#dbe5e1] pl-3.5 pr-20 text-sm font-semibold outline-none focus:border-[#0cb48c] focus:ring-2 focus:ring-[#0cb48c]/15">
            <span id="rename-file-ext" class="pointer-events-none absolute right-2.5 rounded-lg bg-[#edf6f3] px-2.5 py-1 text-xs font-extrabold text-[#0aa982]">.png</span>
          </div>
          <p class="mt-2 text-[11px] text-[#818e89]">
            Cukup ketik nama saja (misal: <b class="text-[#2a3833]">cup</b>), otomatis tersimpan sebagai <span id="rename-file-example" class="font-extrabold text-[#0aa982]">cup.png</span>.
          </p>
        </label>
        <div class="mt-5 flex gap-2">
          <button type="button" data-action="close-dialog" data-dialog="rename-modal" class="flex h-11 flex-1 items-center justify-center rounded-xl border border-[#dce5e1] text-xs font-bold text-[#44514d] hover:border-[#bfcfc9]">Batal</button>
          <button type="submit" data-action="confirm-rename" class="flex h-11 flex-1 items-center justify-center rounded-xl bg-[#0cb48c] text-xs font-extrabold text-white shadow-[0_4px_14px_rgba(12,180,140,.25)] hover:bg-[#09a37e]">Simpan nama</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Modal konfirmasi keluar akun --}}
  <div id="logout-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-[#10251f]/45 p-4 backdrop-blur-[2px]">
    <div class="w-full max-w-[380px] rounded-[24px] bg-white p-6 text-center shadow-[0_30px_80px_rgba(12,39,31,.22)]">
      <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#fee2e2] text-[#ef4444]">
        <x-icon name="logout" class="h-6 w-6" />
      </div>
      <h2 class="text-lg font-extrabold tracking-[-0.02em] text-[#1a2824]">Konfirmasi Keluar</h2>
      <p class="mt-1.5 text-xs text-[#73827d]">Apakah kamu yakin ingin keluar dari akun <b class="text-[#202e29]">{{ $user['name'] }}</b>?</p>
      <div class="mt-6 flex gap-2.5">
        <button type="button" data-action="close-dialog" data-dialog="logout-modal" class="flex h-11 flex-1 items-center justify-center rounded-xl border border-[#dce5e1] text-xs font-bold text-[#44514d] hover:border-[#bfcfc9]">Batal</button>
        <form method="POST" action="{{ route('logout') }}" class="flex-1">
          @csrf
          <button type="submit" class="flex h-11 w-full items-center justify-center rounded-xl bg-[#e05252] text-xs font-extrabold text-white shadow-[0_4px_14px_rgba(224,82,82,.25)] hover:bg-[#c93b3b] transition-colors">Ya, Keluar</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Modal pratinjau file --}}
  <div id="preview-modal" hidden class="fixed inset-0 z-50 flex flex-col bg-black/80 backdrop-blur-sm">
    {{-- Header --}}
    <div class="flex shrink-0 items-center gap-3 border-b border-white/10 bg-[#111f1b]/90 px-5 py-3.5">
      <div id="preview-icon" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white">
        <x-icon name="file" class="h-5 w-5" />
      </div>
      <div class="min-w-0 flex-1">
        <p id="preview-name" class="truncate text-sm font-extrabold text-white"></p>
        <p id="preview-meta" class="mt-0.5 text-[10px] text-white/50"></p>
      </div>
      <a id="preview-download" href="#" class="flex items-center gap-1.5 rounded-xl bg-white/10 px-3.5 py-2 text-[11px] font-bold text-white transition hover:bg-white/20">
        <x-icon name="download" class="h-4 w-4" /> Unduh
      </a>
      <button type="button" data-action="close-preview" aria-label="Tutup pratinjau" class="rounded-xl p-2 text-white/60 hover:bg-white/10 hover:text-white">
        <x-icon name="x" class="h-5 w-5" />
      </button>
    </div>
    {{-- Konten pratinjau --}}
    <div id="preview-body" class="flex min-h-0 flex-1 items-center justify-center overflow-auto p-4 md:p-8">
      {{-- Gambar --}}
      <img id="preview-img" src="" alt="" hidden class="max-h-full max-w-full rounded-xl object-contain shadow-2xl">
      {{-- PDF --}}
      <iframe id="preview-pdf" src="" hidden class="h-full w-full rounded-xl border-0" style="min-height:70vh"></iframe>
      {{-- Video --}}
      <video id="preview-video" src="" controls hidden class="max-h-full max-w-full rounded-xl shadow-2xl" style="max-height:75vh"></video>
      {{-- Audio --}}
      <div id="preview-audio-wrap" hidden class="flex flex-col items-center gap-6 text-white">
        <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-white/10">
          <x-icon name="music" class="h-10 w-10" />
        </div>
        <audio id="preview-audio" src="" controls class="w-72"></audio>
      </div>
      {{-- Teks --}}
      <pre id="preview-text" hidden class="max-h-[75vh] w-full max-w-4xl overflow-auto rounded-xl bg-[#0f1f1b] p-5 text-xs leading-6 text-[#cde8e0]"></pre>
      {{-- Tidak didukung --}}
      <div id="preview-unsupported" hidden class="flex flex-col items-center gap-4 text-center text-white">
        <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-white/10">
          <x-icon name="file" class="h-10 w-10" />
        </div>
        <p class="text-base font-extrabold">Pratinjau tidak tersedia</p>
        <p class="text-sm text-white/50">Format file ini tidak bisa ditampilkan.<br>Silakan unduh untuk membukanya.</p>
        <a id="preview-unsupported-dl" href="#" class="mt-2 flex items-center gap-2 rounded-xl bg-[#0cb48c] px-5 py-2.5 text-sm font-extrabold text-white hover:bg-[#09a37e]">
          <x-icon name="download" class="h-4 w-4" /> Unduh file
        </a>
      </div>
      {{-- Loading --}}
      <div id="preview-loading" class="flex flex-col items-center gap-3 text-white/60">
        <svg class="h-8 w-8 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
        <p class="text-sm">Memuat pratinjau…</p>
      </div>
    </div>
  </div>

  <div id="toast" hidden class="fixed left-1/2 top-5 z-[60] -translate-x-1/2 rounded-xl bg-[#18352d] px-4 py-3 text-xs font-bold text-white shadow-xl"></div>
</div>
</body>
</html>
