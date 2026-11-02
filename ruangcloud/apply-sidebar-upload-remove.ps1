# Sidebar: hapus tombol "+ Unggah berkas baru" dari sidebar pengguna.
# Setelah tombol ini hilang, menu Beranda menjadi item paling atas di
# bawah logo. Unggah tetap bisa dari tombol Unggah berkas di halaman
# File saya / di dalam folder.
# Jalankan dari root proyek:
#   powershell -ExecutionPolicy Bypass -File .\apply-sidebar-upload-remove.ps1
# Setelah itu:  docker compose up -d --build   lalu Ctrl+F5.

$ErrorActionPreference = 'Stop'
$root = $PSScriptRoot

function Replace-Exact($relPath, $old, $new) {
    $path = Join-Path $root $relPath
    $text = [IO.File]::ReadAllText($path)
    if (-not $text.Contains($old)) {
        $oldC = $old -replace "`n", "`r`n"
        $newC = $new -replace "`n", "`r`n"
        if ($text.Contains($oldC)) { $old = $oldC; $new = $newC }
    }
    if (-not $text.Contains($old)) {
        Write-Host "GAGAL: pola lama tidak ditemukan di $relPath -- kabari Muse." -ForegroundColor Red
        exit 1
    }
    [IO.File]::WriteAllText($path, $text.Replace($old, $new))
    Write-Host "OK: $relPath" -ForegroundColor Green
}

$old = @'
    {{-- Tombol Unggah Baru --}}
    <button type="button" data-action="open-upload" class="mb-6 flex h-12 items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-400 text-sm font-extrabold text-white shadow-lg shadow-emerald-500/30 ring-1 ring-white/25 transition-all hover:from-emerald-400 hover:to-teal-300 active:scale-[0.98]">
      <x-icon name="plus" class="h-5 w-5" /> Unggah berkas baru
    </button>

    {{-- Menu Navigasi --}}
'@

$new = @'
    {{-- Menu Navigasi --}}
'@

Replace-Exact 'resources\views\components\user-layout.blade.php' $old $new
Write-Host "Langkah berikutnya: docker compose up -d --build  lalu Ctrl+F5 di browser."
