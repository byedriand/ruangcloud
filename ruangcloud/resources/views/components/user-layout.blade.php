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
<div class="min-h-screen bg-[#f8faf9] text-slate-900">
  <button type="button" id="sidebar-backdrop" hidden aria-label="Tutup menu" data-action="close-sidebar" class="fixed inset-0 z-30 bg-slate-900/30 backdrop-blur-sm lg:hidden"></button>

  {{-- Sidebar --}}
  <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-[264px] -translate-x-full flex-col bg-[#0f2e26] px-5 pb-5 pt-6 text-white transition-transform duration-300 lg:translate-x-0">
    <div class="mb-6 flex items-center justify-between px-1">
      <a href="{{ route('dashboard') }}" class="focus:outline-none"><x-brand :light="true" /></a>
      <button type="button" data-action="close-sidebar" class="rounded-lg p-1.5 text-slate-300 hover:bg-white/10 hover:text-white lg:hidden">
        <x-icon name="x" class="h-5 w-5" />
      </button>
    </div>

    {{-- Tombol Unggah Baru --}}
    <button type="button" data-action="open-upload" class="mb-6 flex h-12 items-center justify-center gap-2.5 rounded-2xl bg-emerald-500 text-sm font-extrabold text-white shadow-lg shadow-emerald-950/40 transition-all hover:bg-emerald-400 active:scale-[0.98]">
      <x-icon name="plus" class="h-5 w-5" /> Unggah berkas baru
    </button>

    {{-- Menu Navigasi --}}
    <nav class="space-y-1.5">
      @foreach ($nav as $item)
        <a href="{{ route($item['route']) }}"
           class="flex h-11 w-full items-center gap-3.5 rounded-xl px-3.5 text-[15px] font-semibold transition-all {{ $active === $item['key'] ? 'bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
          <x-icon :name="$item['icon']" class="h-5 w-5 {{ $active === $item['key'] ? 'text-white' : 'text-emerald-300/80' }}" />
          {{ $item['label'] }}
          @if ($item['badge'])
            <span class="ml-auto rounded-full {{ $active === $item['key'] ? 'bg-white/20 text-white' : 'bg-white/10 text-slate-300' }} px-2.5 py-0.5 text-xs font-bold">{{ $item['badge'] }}</span>
          @endif
        </a>
      @endforeach
    </nav>

    {{-- Widget Penyimpanan --}}
    <div class="mt-auto rounded-2xl border border-white/10 bg-white/5 p-4">
      <div class="mb-2.5 flex items-center justify-between text-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-200">Penyimpanan</span>
        <span class="font-black text-emerald-400">{{ $storage['percent'] }}%</span>
      </div>
      <div class="h-2 overflow-hidden rounded-full bg-white/15">
        <div class="h-full rounded-full transition-all duration-500 {{ $storage['percent'] > 85 ? 'bg-amber-400' : 'bg-gradient-to-r from-emerald-400 to-teal-300' }}" style="width: {{ $storage['percent'] }}%"></div>
      </div>
      <p class="mt-2.5 text-xs text-slate-300"><b class="font-bold text-white">{{ $storage['used'] }}</b> dari {{ $storage['total'] }} terpakai</p>
      @if ($storage['percent'] > 85)
        <p class="mt-2 text-xs font-bold text-amber-300">Penyimpanan hampir penuh. Bersihkan file sampah.</p>
      @endif
    </div>

    {{-- Profil Pengguna & Keluar --}}
    <button type="button" data-action="open-logout" title="Keluar dari akun" class="group mt-4 flex w-full items-center gap-3 rounded-2xl border border-transparent p-2 text-left transition-colors hover:bg-white/10">
      <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-sm font-extrabold text-white">{{ $user['initials'] }}</div>
      <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-bold text-white">{{ $user['name'] }}</p>
        <p class="truncate text-xs text-slate-300">{{ $user['email'] }}</p>
      </div>
      <span class="rounded-lg bg-rose-500/20 px-2.5 py-1 text-xs font-bold text-rose-300 transition hover:bg-rose-500/30 group-hover:bg-rose-500/30">Keluar</span>
    </button>
  </aside>

  {{-- Konten Utama --}}
  <div class="lg:pl-[264px]">
    {{-- Header Atas --}}
    <header class="sticky top-0 z-20 flex h-[76px] items-center gap-4 border-b border-slate-200/90 bg-white/95 px-5 backdrop-blur md:px-8">
      <button type="button" aria-label="Buka menu" data-action="open-sidebar" class="rounded-xl p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
        <x-icon name="menu" class="h-6 w-6" />
      </button>

      <form method="GET" action="{{ route('files.index') }}" class="relative max-w-[480px] flex-1">
        <x-icon name="search" class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
        <input id="search" name="q" value="{{ request('q') }}" type="text" autocomplete="off" class="h-11 w-full rounded-2xl border border-slate-200 bg-slate-50/80 pl-11 pr-4 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10" placeholder="Cari file atau folder, tekan Enter...">
      </form>

      <div class="ml-auto flex items-center gap-3">
        <a href="{{ route('activity.index') }}" aria-label="Aktivitas" class="relative rounded-xl p-2.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800">
          <x-icon name="bell" class="h-5 w-5" />
        </a>
        <div class="hidden h-8 w-px bg-slate-200 sm:block"></div>
        <div class="hidden h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-xs font-extrabold text-emerald-800 sm:flex">
          {{ $user['initials'] }}
        </div>
      </div>
    </header>

    {{-- Main Container --}}
    <main class="mx-auto max-w-[1440px] px-5 py-7 md:px-8 md:py-9">
      {{ $slot }}
    </main>
  </div>

  {{-- Kartu Progres Unggahan (diisi uploader.js) --}}
  <div id="upload-card" hidden class="fixed bottom-6 right-6 z-30 w-[350px] overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xl shadow-slate-900/15 sm:right-8">
    <div class="flex items-center gap-3.5">
      <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
        <x-icon name="upload" class="h-5 w-5" />
      </div>
      <div class="min-w-0 flex-1">
        <div class="flex items-center justify-between">
          <p id="up-name" class="truncate text-sm font-bold text-slate-900">-</p>
          <span id="up-status" class="text-xs font-extrabold text-emerald-600">0%</span>
        </div>
        <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
          <div id="up-bar" class="upload-progress h-full rounded-full bg-emerald-500" style="width: 0%"></div>
        </div>
        <p id="up-meta" class="mt-1.5 text-xs text-slate-500"></p>
      </div>
      <button type="button" id="up-toggle" data-action="toggle-upload" aria-label="Jeda unggahan" class="rounded-xl p-2 text-slate-500 hover:bg-slate-100">
        <span id="icon-pause"><x-icon name="pause" class="h-4.5 w-4.5" /></span>
        <span id="icon-resume" hidden><x-icon name="upload" class="h-4.5 w-4.5" /></span>
      </button>
      <button type="button" data-action="cancel-upload" aria-label="Batalkan unggahan" class="rounded-xl p-2 text-slate-500 hover:bg-slate-100">
        <x-icon name="x" class="h-4.5 w-4.5" />
      </button>
    </div>
  </div>

  {{-- Modal Upload --}}
  <div id="upload-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
    <div class="w-full max-w-[500px] rounded-3xl bg-white p-6 shadow-2xl md:p-7">
      <div class="mb-5 flex items-start justify-between">
        <div>
          <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Unggah file</h2>
          <p class="mt-1 text-sm text-slate-500">File besar akan diunggah via chunk resumable otomatis.</p>
        </div>
        <button type="button" data-action="close-upload" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
          <x-icon name="x" class="h-5 w-5" />
        </button>
      </div>

      <label id="drop-zone" class="flex cursor-pointer flex-col items-center rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50/40 px-6 py-10 text-center transition hover:border-emerald-500 hover:bg-emerald-50/70">
        <input id="upload-input" type="file" class="hidden">
        <div class="mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
          <x-icon name="upload" class="h-7 w-7" />
        </div>
        <p class="text-base font-bold text-slate-800">Tarik dan jatuhkan file ke sini</p>
        <p class="mt-1 text-sm text-slate-500">atau <span class="font-bold text-emerald-600 hover:underline">pilih dari komputer Anda</span></p>
        <span class="mt-4 rounded-full bg-white px-3.5 py-1 text-xs font-semibold text-slate-600 shadow-sm border border-slate-200/80">Maksimum 5 GB per file</span>
      </label>

      <div class="mt-4 flex items-center gap-3 rounded-2xl bg-slate-50 p-3.5 text-xs text-slate-600 border border-slate-200/80">
        <x-icon name="cloud" class="h-5 w-5 shrink-0 text-emerald-600" />
        <p>Unggahan terdistribusi multi-node dengan verifikasi checksum SHA-256 otomatis.</p>
      </div>
    </div>
  </div>

  {{-- Modal Bagikan --}}
  <div id="share-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
    <div class="w-full max-w-[460px] rounded-3xl bg-white p-6 shadow-2xl md:p-7">
      <div class="mb-5 flex items-start justify-between">
        <div class="min-w-0">
          <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Bagikan file</h2>
          <p id="share-file-name" class="mt-1 truncate text-sm text-slate-500"></p>
        </div>
        <button type="button" data-action="close-dialog" data-dialog="share-modal" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
          <x-icon name="x" class="h-5 w-5" />
        </button>
      </div>
      <div id="share-form" class="space-y-4">
        <label class="block">
          <span class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600">Kata sandi (opsional)</span>
          <input id="share-password" type="text" autocomplete="off" placeholder="Kosongkan bila tanpa kata sandi" class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm font-medium outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
        </label>
        <label class="block">
          <span class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600">Masa berlaku tautan</span>
          <select id="share-expiry" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-medium outline-none focus:border-emerald-500">
            <option value="">Tanpa batas waktu</option>
            <option value="1">1 hari</option>
            <option value="7" selected>7 hari</option>
            <option value="30">30 hari</option>
          </select>
        </label>
        <button type="button" data-action="create-share" class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 hover:bg-emerald-500">
          <x-icon name="link" class="h-4.5 w-4.5" /> Buat tautan berbagi
        </button>
      </div>
      <div id="share-result" hidden class="space-y-3">
        <p class="text-sm font-bold text-emerald-700">Tautan siap dibagikan:</p>
        <div class="flex gap-2">
          <input id="share-url" readonly class="h-11 min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-xs font-medium text-slate-800 outline-none">
          <button type="button" data-action="copy-share" class="flex h-11 items-center gap-1.5 rounded-xl bg-emerald-600 px-4 text-xs font-bold text-white hover:bg-emerald-500">
            <x-icon name="copy" class="h-4 w-4" /> Salin
          </button>
        </div>
        <p class="text-xs text-slate-500">Anda dapat mencabut izin akses tautan kapan saja di halaman Dibagikan.</p>
      </div>
    </div>
  </div>

  {{-- Modal Pindahkan --}}
  <div id="move-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
    <div class="w-full max-w-[420px] rounded-3xl bg-white p-6 shadow-2xl md:p-7">
      <div class="mb-5 flex items-start justify-between">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Pindahkan file</h2>
        <button type="button" data-action="close-dialog" data-dialog="move-modal" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
          <x-icon name="x" class="h-5 w-5" />
        </button>
      </div>
      <label class="block">
        <span class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600">Pilih folder tujuan</span>
        <select id="move-folder" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-medium outline-none focus:border-emerald-500">
          <option value="">File saya (folder utama)</option>
          @foreach ($folders as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach
        </select>
      </label>
      <button type="button" data-action="confirm-move" class="mt-5 flex h-11 w-full items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 hover:bg-emerald-500">
        Pindahkan ke folder ini
      </button>
    </div>
  </div>

  {{-- Modal Ubah Nama File --}}
  <div id="rename-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
    <div class="w-full max-w-[460px] rounded-3xl bg-white p-6 shadow-2xl md:p-7">
      <div class="mb-4 flex items-start justify-between">
        <div>
          <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Ubah nama file</h2>
          <p class="mt-1 text-sm text-slate-500">Ekstensi file otomatis dipertahankan untuk keamanan.</p>
        </div>
        <button type="button" data-action="close-dialog" data-dialog="rename-modal" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
          <x-icon name="x" class="h-5 w-5" />
        </button>
      </div>

      <div class="mb-4 flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5">
        <span class="text-xs font-semibold text-slate-600">Tipe berkas:</span>
        <span id="rename-file-type" class="rounded-lg bg-white px-3 py-1 text-xs font-bold text-emerald-700 shadow-sm border border-slate-200/80">Foto PNG</span>
      </div>

      <form id="rename-form" onsubmit="event.preventDefault(); window.confirmRenameFile?.();">
        <label class="block">
          <span class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600">Nama file baru</span>
          <div class="relative flex items-center">
            <input id="rename-file-input" type="text" autocomplete="off" class="h-11 w-full rounded-xl border border-slate-200 pl-4 pr-24 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
            <span id="rename-file-ext" class="pointer-events-none absolute right-2.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">.png</span>
          </div>
          <p class="mt-2 text-xs text-slate-500">
            Cukup ketik nama saja (misal: <b class="font-bold text-slate-800">laporan_final</b>), otomatis tersimpan sebagai <span id="rename-file-example" class="font-bold text-emerald-700">laporan_final.png</span>.
          </p>
        </label>
        <div class="mt-6 flex gap-3">
          <button type="button" data-action="close-dialog" data-dialog="rename-modal" class="flex h-11 flex-1 items-center justify-center rounded-xl border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50">Batal</button>
          <button type="submit" data-action="confirm-rename" class="flex h-11 flex-1 items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 hover:bg-emerald-500">Simpan nama</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Modal Konfirmasi Keluar Akun --}}
  <div id="logout-modal" hidden class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
    <div class="w-full max-w-[400px] rounded-3xl bg-white p-7 text-center shadow-2xl">
      <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
        <x-icon name="logout" class="h-7 w-7" />
      </div>
      <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Konfirmasi Keluar</h2>
      <p class="mt-2 text-sm text-slate-500">Apakah Anda yakin ingin keluar dari akun <b class="text-slate-800">{{ $user['name'] }}</b>?</p>
      <div class="mt-6 flex gap-3">
        <button type="button" data-action="close-dialog" data-dialog="logout-modal" class="flex h-11 flex-1 items-center justify-center rounded-xl border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50">Batal</button>
        <form method="POST" action="{{ route('logout') }}" class="flex-1">
          @csrf
          <button type="submit" class="flex h-11 w-full items-center justify-center rounded-xl bg-rose-600 text-sm font-bold text-white shadow-lg shadow-rose-600/25 hover:bg-rose-500 transition-colors">Ya, Keluar</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Modal Pratinjau File --}}
  <div id="preview-modal" hidden class="fixed inset-0 z-50 flex flex-col bg-slate-950/85 backdrop-blur-md">
    <div class="flex shrink-0 items-center gap-3.5 border-b border-white/10 bg-slate-900/90 px-6 py-4">
      <div id="preview-icon" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white">
        <x-icon name="file" class="h-5 w-5" />
      </div>
      <div class="min-w-0 flex-1">
        <p id="preview-name" class="truncate text-base font-bold text-white"></p>
        <p id="preview-meta" class="mt-0.5 text-xs text-white/60"></p>
      </div>
      <a id="preview-download" href="#" class="flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-xs font-bold text-white transition hover:bg-white/20">
        <x-icon name="download" class="h-4 w-4" /> Unduh
      </a>
      <button type="button" data-action="close-preview" aria-label="Tutup pratinjau" class="rounded-xl p-2 text-white/60 hover:bg-white/10 hover:text-white">
        <x-icon name="x" class="h-5 w-5" />
      </button>
    </div>
    <div id="preview-body" class="flex min-h-0 flex-1 items-center justify-center overflow-auto p-4 md:p-8">
      <img id="preview-img" src="" alt="" hidden class="max-h-full max-w-full rounded-2xl object-contain shadow-2xl">
      <iframe id="preview-pdf" src="" hidden class="h-full w-full rounded-2xl border-0" style="min-height:75vh"></iframe>
      <video id="preview-video" src="" controls hidden class="max-h-full max-w-full rounded-2xl shadow-2xl" style="max-height:75vh"></video>
      <div id="preview-audio-wrap" hidden class="flex flex-col items-center gap-6 text-white">
        <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-white/10">
          <x-icon name="music" class="h-10 w-10" />
        </div>
        <audio id="preview-audio" src="" controls class="w-80"></audio>
      </div>
      <pre id="preview-text" hidden class="max-h-[75vh] w-full max-w-4xl overflow-auto rounded-2xl bg-slate-900 p-6 text-xs leading-relaxed text-slate-200 font-mono"></pre>
      <div id="preview-unsupported" hidden class="flex flex-col items-center gap-4 text-center text-white">
        <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-white/10">
          <x-icon name="file" class="h-10 w-10 text-white/80" />
        </div>
        <p class="text-lg font-bold">Pratinjau tidak tersedia</p>
        <p class="text-sm text-white/60">Format file ini belum mendukung pratinjau langsung.<br>Silakan unduh untuk membukanya.</p>
        <a id="preview-unsupported-dl" href="#" class="mt-2 flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-500">
          <x-icon name="download" class="h-4 w-4" /> Unduh file
        </a>
      </div>
      <div id="preview-loading" class="flex flex-col items-center gap-3 text-white/60">
        <svg class="h-9 w-9 animate-spin text-emerald-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
        <p class="text-sm font-medium">Memuat pratinjau…</p>
      </div>
    </div>
  </div>

  {{-- Toast Pemberitahuan --}}
  <div id="toast" hidden class="fixed left-1/2 top-6 z-[60] -translate-x-1/2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-2xl"></div>
</div>
</body>
</html>
