# Perbaikan meter kekuatan kata sandi di halaman register.
# Gejala: label Sangat lemah/Lemah/Cukup/Kuat dan batang meter tidak bergerak
# saat mengetik (handler inline oninput pada view tidak berjalan), sementara
# tombol Lihat (yang diikat bundle ruangcloud.js) bekerja. Obatnya: logika
# meter + cek kecocokan + spinner submit dipindah ke bundle ruangcloud.js
# dan atribut oninput lama dilepas.
# Jalankan dari root proyek:
#   powershell -ExecutionPolicy Bypass -File .\apply-strength-fix.ps1
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

$o1 = @'
// Pulihkan unggahan yang masih berjalan/terjeda dari halaman sebelumnya (bila ada).
restoreUploads();
'@

$n1 = @'
// Pulihkan unggahan yang masih berjalan/terjeda dari halaman sebelumnya (bila ada).
restoreUploads();

// -- Halaman register: meter kekuatan kata sandi, cek kecocokan, spinner --
// Diikat lewat bundle ini (jalur yang sama dengan toggle Lihat yang terbukti
// jalan) karena handler inline pada view register tidak berjalan di semua
// lingkungan. Hanya aktif bila elemen-elemen register ada di halaman.
(function () {
    const pw = document.getElementById('password');
    const barsEl = document.getElementById('strength-bars');
    if (!pw || !barsEl) return;

    const pw2 = document.getElementById('password_confirmation');
    const bars = ['sb1', 'sb2', 'sb3', 'sb4'].map((id) => document.getElementById(id));
    const label = document.getElementById('strength-label');
    const matchLabel = document.getElementById('match-label');
    const colors = ['bg-red-400', 'bg-orange-400', 'bg-amber-400', 'bg-emerald-500'];
    const labels = ['Sangat lemah', 'Lemah', 'Cukup', 'Kuat'];
    const labelColors = ['text-red-500', 'text-orange-500', 'text-amber-600', 'text-emerald-600'];

    function checkMatch() {
        if (!pw2 || !matchLabel) return;
        if (pw2.value.length === 0) { matchLabel.classList.add('hidden'); return; }
        matchLabel.classList.remove('hidden');
        if (pw.value === pw2.value) {
            matchLabel.textContent = 'Kata sandi cocok';
            matchLabel.className = 'mt-1.5 text-xs font-bold text-emerald-600';
        } else {
            matchLabel.textContent = 'Kata sandi tidak cocok';
            matchLabel.className = 'mt-1.5 text-xs font-bold text-red-500';
        }
    }

    function checkStrength() {
        const val = pw.value;
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        bars.forEach((b, i) => {
            if (b) b.className = 'h-1.5 flex-1 rounded-full transition-all ' + (i < score ? colors[score - 1] : 'bg-slate-200');
        });
        if (label) {
            if (val.length === 0) {
                label.textContent = 'Masukkan kata sandi untuk melihat kekuatannya';
                label.className = 'mt-1.5 text-xs text-[#64748b]';
            } else {
                label.textContent = labels[score - 1] || 'Sangat lemah';
                label.className = 'mt-1.5 text-xs font-bold ' + (labelColors[score - 1] || 'text-red-500');
            }
        }
        checkMatch();
    }

    pw.addEventListener('input', checkStrength);
    if (pw2) pw2.addEventListener('input', checkMatch);
    checkStrength();

    const form = document.getElementById('register-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const terms = document.getElementById('terms');
            if (terms && !terms.checked) {
                e.preventDefault();
                terms.closest('label').classList.add('border-red-400', 'bg-red-50/50');
                setTimeout(() => terms.closest('label').classList.remove('border-red-400', 'bg-red-50/50'), 2000);
                return;
            }
            const btnText = document.getElementById('btn-text');
            if (btnText) btnText.classList.add('invisible');
            const chev = document.querySelector('.group svg.h-4');
            if (chev) chev.classList.add('invisible');
            const spinner = document.getElementById('btn-spinner');
            if (spinner) spinner.classList.remove('hidden');
            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn) submitBtn.disabled = true;
        });
    }
})();
'@

$o2 = @'
oninput="checkStrength(this.value)"
'@

$n2 = @'

'@

$o3 = @'
oninput="checkMatch()"
'@

$n3 = @'

'@

Replace-Exact 'resources\js\ruangcloud.js' $o1 $n1
Replace-Exact 'resources\views\auth\register.blade.php' $o2 $n2
Replace-Exact 'resources\views\auth\register.blade.php' $o3 $n3
Write-Host "Langkah berikutnya: docker compose up -d --build  lalu Ctrl+F5 di browser."
