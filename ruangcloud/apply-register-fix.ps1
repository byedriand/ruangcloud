# Perbaikan halaman register & kuota:
#  1. Selesai daftar -> diarahkan ke halaman LOGIN (tidak auto-login ke
#     dashboard), pesan sukses tampil sebagai popup beranimasi di sana.
#  2. Tombol Lihat kata sandi register mati karena toggle diikat dua kali
#     (script inline + handler global ruangcloud.js) -- inline dilepas,
#     handler global dibuat men-toggle input di kotak yang sama, dan kolom
#     konfirmasi kini punya tombol Lihat juga.
#  3. Kuota bawaan akun baru & user seed: 100 GB -> 5 GB, teks ikut diubah.
# Jalankan dari root proyek:
#   powershell -ExecutionPolicy Bypass -File .\apply-register-fix.ps1
# Setelah itu:  docker compose up -d --build

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
            'quota_bytes'  => 100 * 1024 ** 3, // 100 GB
'@

$n1 = @'
            'quota_bytes'  => 5 * 1024 ** 3, // 5 GB
'@

$pairs += @{ Path = 'app\Http\Controllers\AuthController.php'; Old = $o1; New = $n1 }

$o2 = @'
        ActivityLog::record('login', Str::before($user->name, ' ') . ' mendaftar dan masuk ke sistem', [], $user->id);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat! Selamat datang di RuangCloud.');
'@

$n2 = @'
        ActivityLog::record('login', Str::before($user->name, ' ') . ' mendaftar akun baru', [], $user->id);

        // Setelah daftar TIDAK langsung masuk: arahkan ke halaman login
        // membawa pesan sukses (popup beranimasi tampil di sana).
        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan masuk dengan akun barumu untuk mulai menggunakan RuangCloud.');
'@

$pairs += @{ Path = 'app\Http\Controllers\AuthController.php'; Old = $o2; New = $n2 }

$o3 = @'
  100,  
'@

$n3 = @'
  5,    
'@

$pairs += @{ Path = 'database\seeders\RuangCloudSeeder.php'; Old = $o3; New = $n3 }

$o4 = @'
| 100 GB |
'@

$n4 = @'
| 5 GB |
'@

$pairs += @{ Path = 'README.md'; Old = $o4; New = $n4 }

$o5 = @'
'title' => '100 GB Gratis'
'@

$n5 = @'
'title' => '5 GB Gratis'
'@

$pairs += @{ Path = 'resources\views\auth\register.blade.php'; Old = $o5; New = $n5 }

$o6 = @'
document.querySelectorAll('[data-action="toggle-password"]').forEach(btn => {
  btn.addEventListener('click', () => {
    const pw = document.getElementById('password');
    const isHidden = pw.type === 'password';
    pw.type = isHidden ? 'text' : 'password';
    btn.textContent = isHidden ? 'Sembunyikan' : 'Lihat';
  });
'@

$n6 = @'
// Toggle Lihat/Sembunyikan kata sandi ditangani global oleh ruangcloud.js
// lewat data-action="toggle-password" -- jangan diikat lagi di sini, klik
// akan memicu dua kali dan saling meniadakan (tombol terlihat mati).
'@

$pairs += @{ Path = 'resources\views\auth\register.blade.php'; Old = $o6; New = $n6 }

$o7 = @'
<input id="password_confirmation" type="password" name="password_confirmation"
                   placeholder="Ulangi kata sandi"
                   autocomplete="new-password"
                   class="h-12 w-full rounded-xl border border-white/80 bg-white/60 pl-11 pr-4 text-sm shadow-[inset_0_1px_0_rgba(255,255,255,.9)] outline-none backdrop-blur-md transition placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white/85 focus:ring-4 focus:ring-emerald-500/10"
                   oninput="checkMatch()">
          </div>
'@

$n7 = @'
<input id="password_confirmation" type="password" name="password_confirmation"
                   placeholder="Ulangi kata sandi"
                   autocomplete="new-password"
                   class="h-12 w-full rounded-xl border border-white/80 bg-white/60 pl-11 pr-14 text-sm shadow-[inset_0_1px_0_rgba(255,255,255,.9)] outline-none backdrop-blur-md transition placeholder:text-slate-400 focus:border-emerald-400 focus:bg-white/85 focus:ring-4 focus:ring-emerald-500/10"
                   oninput="checkMatch()">
            <button type="button" data-action="toggle-password"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[#078d6d]">Lihat</button>
          </div>
'@

$pairs += @{ Path = 'resources\views\auth\register.blade.php'; Old = $o7; New = $n7 }

$o8 = @'
<p class="mt-6 text-center text-xs text-[#64748b]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-[#078d6d] hover:underline">Daftar sekarang</a>
      </p>
    </div>
  </div>
</div>
@endsection
'@

$n8 = @'
<p class="mt-6 text-center text-xs text-[#64748b]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-[#078d6d] hover:underline">Daftar sekarang</a>
      </p>
    </div>
  </div>
</div>

@include('components.success-popup')
@endsection
'@

$pairs += @{ Path = 'resources\views\auth\login.blade.php'; Old = $o8; New = $n8 }

$o9 = @'
Kuota 100 GB
'@

$n9 = @'
Kuota 5 GB
'@

$pairs += @{ Path = 'resources\views\landing.blade.php'; Old = $o9; New = $n9 }

$o10 = @'
        case 'toggle-password': {
            const input = $('#password');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            target.textContent = show ? 'Sembunyikan' : 'Lihat';
            break;
        }
'@

$n10 = @'
        case 'toggle-password': {
            const wrap = target.closest('div');
            const input = (wrap && wrap.querySelector('input')) || $('#password');
            if (!input) break;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            target.textContent = show ? 'Sembunyikan' : 'Lihat';
            break;
        }
'@

$pairs += @{ Path = 'resources\js\ruangcloud.js'; Old = $o10; New = $n10 }

foreach ($p in $pairs) { Replace-Exact $p.Path $p.Old $p.New }
Write-Host "Langkah berikutnya: docker compose up -d --build"
