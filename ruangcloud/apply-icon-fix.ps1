# Perjelas ikon kolom formulir (register, login, profil).
# Ikon lama menempel polos berwarna abu/hijau pucat di atas kaca terang
# sehingga nyaris tak terlihat. Kini setiap ikon duduk di dalam lencana
# (chip) hijau emerald dengan ring -- kontras jelas dan seragam di semua
# form. Form login yang belum punya ikon kini ditambah ikon juga.
# Jalankan dari root proyek:
#   powershell -ExecutionPolicy Bypass -File .\apply-icon-fix.ps1
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
            <x-icon name="user" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
'@

$n1 = @'
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="user" class="h-4 w-4" /></span>
'@

$pairs += @{ Path = 'resources\views\auth\register.blade.php'; Old = $o1; New = $n1 }

$o2 = @'
            <x-icon name="mail" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
'@

$n2 = @'
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="mail" class="h-4 w-4" /></span>
'@

$pairs += @{ Path = 'resources\views\auth\register.blade.php'; Old = $o2; New = $n2 }

$o3 = @'
            <x-icon name="lock" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
'@

$n3 = @'
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="lock" class="h-4 w-4" /></span>
'@

$pairs += @{ Path = 'resources\views\auth\register.blade.php'; Old = $o3; New = $n3 }

$o4 = @'
pl-11
'@

$n4 = @'
pl-12
'@

$pairs += @{ Path = 'resources\views\auth\register.blade.php'; Old = $o4; New = $n4 }

$o5 = @'
          <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="h-12 w-full rounded-xl border border-white/80 bg-white/60 px-4 text-sm shadow-[inset_0_1px_0_rgba(255,255,255,.9)] outline-none backdrop-blur-md transition placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white/85 focus:ring-4 focus:ring-emerald-500/10">
'@

$n5 = @'
          <div class="relative">
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="mail" class="h-4 w-4" /></span>
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="h-12 w-full rounded-xl border border-white/80 bg-white/60 pl-12 text-sm shadow-[inset_0_1px_0_rgba(255,255,255,.9)] outline-none backdrop-blur-md transition placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white/85 focus:ring-4 focus:ring-emerald-500/10">
          </div>
'@

$pairs += @{ Path = 'resources\views\auth\login.blade.php'; Old = $o5; New = $n5 }

$o6 = @'
            <input id="password" type="password"
'@

$n6 = @'
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="lock" class="h-4 w-4" /></span>
            <input id="password" type="password"
'@

$pairs += @{ Path = 'resources\views\auth\login.blade.php'; Old = $o6; New = $n6 }

$o7 = @'
px-4 pr-16
'@

$n7 = @'
pl-12 pr-16
'@

$pairs += @{ Path = 'resources\views\auth\login.blade.php'; Old = $o7; New = $n7 }

$o8 = @'
          <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-600">
            <x-icon name="user" class="h-4.5 w-4.5" />
          </span>
'@

$n8 = @'
          <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30">
            <x-icon name="user" class="h-4 w-4" />
          </span>
'@

$pairs += @{ Path = 'resources\views\profile\partials\forms.blade.php'; Old = $o8; New = $n8 }

$o9 = @'
          <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-600">
            <x-icon name="mail" class="h-4.5 w-4.5" />
          </span>
'@

$n9 = @'
          <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30">
            <x-icon name="mail" class="h-4 w-4" />
          </span>
'@

$pairs += @{ Path = 'resources\views\profile\partials\forms.blade.php'; Old = $o9; New = $n9 }

$o10 = @'
pl-11 pr-4
'@

$n10 = @'
pl-12 pr-4
'@

$pairs += @{ Path = 'resources\views\profile\partials\forms.blade.php'; Old = $o10; New = $n10 }

$o11 = @'
      <div class="relative">
        <input type="password" id="pw-current"
'@

$n11 = @'
      <div class="relative">
        <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="lock" class="h-4 w-4" /></span>
        <input type="password" id="pw-current"
'@

$pairs += @{ Path = 'resources\views\profile\partials\forms.blade.php'; Old = $o11; New = $n11 }

$o12 = @'
          <div class="relative">
            <input type="password" id="pw-new"
'@

$n12 = @'
          <div class="relative">
            <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="lock" class="h-4 w-4" /></span>
            <input type="password" id="pw-new"
'@

$pairs += @{ Path = 'resources\views\profile\partials\forms.blade.php'; Old = $o12; New = $n12 }

$o13 = @'
        <div class="relative">
          <input type="password" id="pw-confirm"
'@

$n13 = @'
        <div class="relative">
          <span class="pointer-events-none absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30"><x-icon name="lock" class="h-4 w-4" /></span>
          <input type="password" id="pw-confirm"
'@

$pairs += @{ Path = 'resources\views\profile\partials\forms.blade.php'; Old = $o13; New = $n13 }

$o14 = @'
px-4 pr-12
'@

$n14 = @'
pl-12 pr-12
'@

$pairs += @{ Path = 'resources\views\profile\partials\forms.blade.php'; Old = $o14; New = $n14 }

foreach ($p in $pairs) { Replace-Exact $p.Path $p.Old $p.New }
Write-Host "Langkah berikutnya: docker compose up -d --build  lalu Ctrl+F5 di browser."
