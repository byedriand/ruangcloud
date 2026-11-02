# apply-storage-detail.ps1 - Detail Penyimpanan: route + widget sidebar bisa diklik
# Jalankan dari root proyek (folder yang ada file artisan):
#   powershell -ExecutionPolicy Bypass -File .\apply-storage-detail.ps1
$ErrorActionPreference = "Stop"
if (-not (Test-Path ".\artisan")) { Write-Host "ERROR: Jalankan dari root proyek (folder artisan)." -ForegroundColor Red; exit 1 }
if (-not (Test-Path ".\app\Http\Controllers\StorageController.php")) { Write-Host "ERROR: Salin dulu StorageController.php ke folder app\Http\Controllers" -ForegroundColor Red; exit 1 }
if (-not (Test-Path ".\resources\views\user\storage.blade.php")) { Write-Host "ERROR: Salin dulu storage.blade.php ke folder resources\views\user" -ForegroundColor Red; exit 1 }
$pairs = @()
$pairs += @{ File = 'routes/web.php'; Old = @'
use App\Http\Controllers\ShareController;
'@; New = @'
use App\Http\Controllers\ShareController;
use App\Http\Controllers\StorageController;
'@ }
$pairs += @{ File = 'routes/web.php'; Old = @'
        Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
'@; New = @'
        Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
        Route::get('/storage', [StorageController::class, 'index'])->name('storage.index');
'@ }
$pairs += @{ File = 'resources/views/components/user-layout.blade.php'; Old = @'
    <div class="mt-auto rounded-2xl border border-white/15 bg-white/[0.07] p-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] backdrop-blur-md">
'@; New = @'
    <a href="{{ route('storage.index') }}" class="group mt-auto block rounded-2xl border p-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] backdrop-blur-md transition {{ $active === 'storage' ? 'border-emerald-300/40 bg-white/[0.12]' : 'border-white/15 bg-white/[0.07] hover:bg-white/[0.11]' }}">
'@ }
$pairs += @{ File = 'resources/views/components/user-layout.blade.php'; Old = @'
      @endif
    </div>

    {{-- Profil Pengguna & Keluar --}}
'@; New = @'
      @endif
      <span class="mt-3 flex items-center gap-1 text-[11px] font-extrabold uppercase tracking-wider text-emerald-300">
        Lihat detail
        <x-icon name="chevron" class="h-3 w-3 transition-transform group-hover:translate-x-0.5" />
      </span>
    </a>

    {{-- Profil Pengguna & Keluar --}}
'@ }
foreach ($p in $pairs) {
  $path = Join-Path "." $p.File
  $text = [IO.File]::ReadAllText($path)
  if ($text.Contains($p.New)) { Write-Host "SUDAH  $($p.File)"; continue }
  if (-not $text.Contains($p.Old)) { Write-Host "GAGAL  $($p.File) - pola lama tidak ditemukan" -ForegroundColor Red; continue }
  [IO.File]::WriteAllText($path, $text.Replace($p.Old, $p.New))
  Write-Host "OK     $($p.File)" -ForegroundColor Green
}
Write-Host ""
Write-Host "Selesai. Tidak perlu npm run build - cukup Ctrl+F5 di browser."
