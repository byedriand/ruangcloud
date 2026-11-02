# Admin Console: rombakan tampilan + Profil pindah ke dalam konsol.
# - Lapisan CSS khusus admin (#admin-console): kartu kaca lebih dalam &
#   hidup (hover terangkat), kepala tabel gradasi emerald, baris tabel
#   menyala saat hover, animasi panel saat berpindah, judul panel
#   gradasi, tombol sidebar bergeser halus saat hover, header berkilau.
# - Menu "Profil Saya" di sidebar admin kini membuka PANEL Profil di
#   dalam Admin Console (tidak lagi pindah ke halaman terpisah), dan
#   setelah menyimpan profil admin kembali ke panel Profil.
# Jalankan dari root proyek:
#   powershell -ExecutionPolicy Bypass -File .\apply-admin-redesign.ps1
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

$pairs = @()
$o1 = @'
.file-blue   { color: #2f66b3; background: linear-gradient(135deg, #e3efff, #b3d2f7); }
'@

$n1 = @'
.file-blue   { color: #2f66b3; background: linear-gradient(135deg, #e3efff, #b3d2f7); }
/* ===== Admin Console: lapisan estetik (hanya di dalam #admin-console) ===== */
#admin-console .glass-card {
  background: linear-gradient(150deg, rgba(255,255,255,0.92), rgba(236,253,245,0.66) 52%, rgba(204,251,241,0.5));
  border-color: rgba(255,255,255,0.9);
  box-shadow: 0 2px 6px rgba(6,40,30,0.05), 0 22px 44px -20px rgba(6,40,30,0.28), inset 0 1px 0 rgba(255,255,255,0.95);
}
#admin-console .stat-lift {
  transition: transform 0.35s cubic-bezier(0.22,1,0.36,1), box-shadow 0.35s cubic-bezier(0.22,1,0.36,1);
}
#admin-console .stat-lift:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 10px rgba(6,40,30,0.06), 0 30px 54px -20px rgba(6,40,30,0.35), inset 0 1px 0 rgba(255,255,255,0.95);
}
@keyframes rcAdminPanelIn {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}
#admin-console .admin-panel:not(.hidden) {
  animation: rcAdminPanelIn 0.45s cubic-bezier(0.22,1,0.36,1) both;
}
#admin-console table thead tr {
  background: linear-gradient(90deg, rgba(16,185,129,0.16), rgba(45,212,191,0.10) 60%, rgba(16,185,129,0.05));
}
#admin-console table thead th {
  color: #065f46;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.09em;
  text-transform: uppercase;
}
#admin-console table tbody tr {
  transition: background-color 0.18s ease;
}
#admin-console table tbody tr:hover {
  background-color: rgba(236,253,245,0.8);
}
#admin-console .admin-nav-btn {
  transition: transform 0.25s cubic-bezier(0.22,1,0.36,1), background-color 0.25s ease, color 0.25s ease, box-shadow 0.25s ease;
}
#admin-console .admin-nav-btn:hover {
  transform: translateX(3px);
}
#admin-console header {
  background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(240,253,250,0.8));
  box-shadow: 0 10px 34px -14px rgba(6,40,30,0.22), inset 0 -1px 0 rgba(255,255,255,0.7);
}
#admin-console ::selection {
  background: rgba(16,185,129,0.3);
}
@supports ((-webkit-background-clip: text) or (background-clip: text)) {
  #admin-console .admin-panel h2 {
    background-image: linear-gradient(92deg, #0f172a 55%, #047857);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
  }
}

'@

$pairs += @{ Path = 'resources\css\ruangcloud.css'; Old = $o1; New = $n1 }

$o2 = @'
<div class="min-h-screen text-slate-900" style=
'@

$n2 = @'
<div id="admin-console" class="min-h-screen text-slate-900" style=
'@

$pairs += @{ Path = 'resources\views\admin\dashboard.blade.php'; Old = $o2; New = $n2 }

$o3 = @'
    <a href="{{ route('profile.edit') }}" class="mt-auto flex w-full items-center gap-3 rounded-2xl border border-white/[0.08] bg-white/[0.05] p-2.5 text-sm font-semibold text-slate-200 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] backdrop-blur-md transition-all hover:bg-white/10 hover:text-white">
      <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/[0.07] text-emerald-300 ring-1 ring-white/10"><x-icon name="user" class="h-4.5 w-4.5" /></span>
      Profil Saya
    </a>
'@

$n3 = @'
    <button type="button" data-panel="Profil" class="admin-nav-btn mt-auto flex w-full items-center gap-3 rounded-2xl border border-white/[0.08] bg-white/[0.05] p-2.5 text-left text-sm font-semibold text-slate-200 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] backdrop-blur-md transition-all hover:bg-white/10 hover:text-white">
      <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/[0.07] text-emerald-300 ring-1 ring-white/10"><x-icon name="user" class="h-4.5 w-4.5" /></span>
      Profil Saya
    </button>
'@

$pairs += @{ Path = 'resources\views\admin\dashboard.blade.php'; Old = $o3; New = $n3 }

$o4 = @'
      {{-- Panel: Audit log --}}
      <div id="panel-Audit log" class="admin-panel hidden">
        @include('admin.partials.panel-auditlog')
      </div>

'@

$n4 = @'
      {{-- Panel: Audit log --}}
      <div id="panel-Audit log" class="admin-panel hidden">
        @include('admin.partials.panel-auditlog')
      </div>

      {{-- Panel: Profil --}}
      <div id="panel-Profil" class="admin-panel hidden">
        <div class="mb-7">
          <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">Profil Admin</h2>
          <p class="mt-1 text-sm text-slate-500">Atur foto, nama pengguna, email, dan kata sandi akun administratormu -- tetap di dalam Admin Console.</p>
        </div>
        <div class="mx-auto w-full max-w-5xl">
          @include('profile.partials.forms', ['user' => auth()->user()])
        </div>
      </div>

'@

$pairs += @{ Path = 'resources\views\admin\dashboard.blade.php'; Old = $o4; New = $n4 }

$o5 = @'
adminLogoutModal?.addEventListener('click', (e) => {
  if (e.target === adminLogoutModal) adminLogoutModal.setAttribute('hidden', '');
});
</script>
@endsection
'@

$n5 = @'
adminLogoutModal?.addEventListener('click', (e) => {
  if (e.target === adminLogoutModal) adminLogoutModal.setAttribute('hidden', '');
});
</script>

@if (session('open_panel') === 'Profil' || $errors->hasAny(['current_password', 'avatar']))
<script>
  switchPanel('Profil');
</script>
@endif
@endsection
'@

$pairs += @{ Path = 'resources\views\admin\dashboard.blade.php'; Old = $o5; New = $n5 }

$o6 = @'
return back()->with('success', 'Profil berhasil diperbarui.');
'@

$n6 = @'
return back()->with('success', 'Profil berhasil diperbarui.')->with('open_panel', 'Profil');
'@

$pairs += @{ Path = 'app\Http\Controllers\ProfileController.php'; Old = $o6; New = $n6 }

$o7 = @'
return back()->with('success', 'Foto profil berhasil diperbarui.');
'@

$n7 = @'
return back()->with('success', 'Foto profil berhasil diperbarui.')->with('open_panel', 'Profil');
'@

$pairs += @{ Path = 'app\Http\Controllers\ProfileController.php'; Old = $o7; New = $n7 }

$o8 = @'
return back()->with('success', 'Foto profil dihapus.');
'@

$n8 = @'
return back()->with('success', 'Foto profil dihapus.')->with('open_panel', 'Profil');
'@

$pairs += @{ Path = 'app\Http\Controllers\ProfileController.php'; Old = $o8; New = $n8 }

$o9 = @'
return back()->with('success', 'Kata sandi berhasil diubah.');
'@

$n9 = @'
return back()->with('success', 'Kata sandi berhasil diubah.')->with('open_panel', 'Profil');
'@

$pairs += @{ Path = 'app\Http\Controllers\ProfileController.php'; Old = $o9; New = $n9 }

$o10 = @'
<article class="rounded-2xl glass-card p-5 shadow-sm transition hover:shadow-md">
'@

$n10 = @'
<article class="rounded-2xl glass-card stat-lift p-5 shadow-sm">
'@

$pairs += @{ Path = 'resources\views\admin\partials\panel-ringkasan.blade.php'; Old = $o10; New = $n10 }

$o11 = @'
rounded-2xl glass-card p-5 shadow-sm
'@

$n11 = @'
rounded-2xl glass-card stat-lift p-5 shadow-sm
'@

$pairs += @{ Path = 'resources\views\admin\partials\panel-pengguna.blade.php'; Old = $o11; New = $n11 }

$o12 = @'
rounded-2xl glass-card p-5 shadow-sm
'@

$n12 = @'
rounded-2xl glass-card stat-lift p-5 shadow-sm
'@

$pairs += @{ Path = 'resources\views\admin\partials\panel-unggahan.blade.php'; Old = $o12; New = $n12 }

$o13 = @'
<div class="rounded-2xl glass-card p-4 text-center shadow-xs">
'@

$n13 = @'
<div class="rounded-2xl glass-card stat-lift p-4 text-center shadow-xs">
'@

$pairs += @{ Path = 'resources\views\admin\partials\panel-auditlog.blade.php'; Old = $o13; New = $n13 }

$o14 = @'
<article class="rounded-3xl glass-card p-6 shadow-sm transition hover:shadow-md">
'@

$n14 = @'
<article class="rounded-3xl glass-card stat-lift p-6 shadow-sm">
'@

$pairs += @{ Path = 'resources\views\admin\partials\panel-penyimpanan.blade.php'; Old = $o14; New = $n14 }

$o15 = @'
<p class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">{{ $storageUsedFmt }}</p>
'@

$n15 = @'
<p class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-600 bg-clip-text text-3xl font-extrabold tracking-tight text-transparent md:text-4xl">{{ $storageUsedFmt }}</p>
'@

$pairs += @{ Path = 'resources\views\admin\partials\panel-penyimpanan.blade.php'; Old = $o15; New = $n15 }

foreach ($p in $pairs) { Replace-Exact $p.Path $p.Old $p.New }
Write-Host "Langkah berikutnya: docker compose up -d --build  lalu Ctrl+F5 di browser."
