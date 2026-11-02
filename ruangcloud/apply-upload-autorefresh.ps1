# Perbaikan: daftar file langsung ter-update setelah unggahan selesai.
# Penyebab lama: uploader me-reload halaman 900ms setelah memanggil /complete,
# padahal server masih merakit file secara async di antrean -- jadi file baru
# belum ada saat halaman dimuat ulang dan baru muncul setelah refresh manual.
# Perbaikan: tunggu (polling) status sesi sampai 'completed' sebelum reload,
# dan job gagal/terhenti tidak lagi menghalangi reload.
# Jalankan dari root proyek:
#   powershell -ExecutionPolicy Bypass -File .\apply-upload-autorefresh.ps1
# Setelah itu:  docker compose up -d --build   lalu Ctrl+F5 di browser.

$ErrorActionPreference = 'Stop'
$root = $PSScriptRoot

$old = @'
    job.state = 'done';
    job.meta = 'Unggahan selesai';
    paint(job);
    notify(`"${job.name}" berhasil diunggah`);
    setTimeout(() => {
        destroyJob(job);
        // Muat ulang daftar file hanya bila tak ada unggahan lain berjalan
        // dan tak ada unduhan terkelola yang akan terputus oleh reload.
        const downloadActive = document.querySelector('[data-action="dl-cancel"]');
        if (jobs.size === 0 && !downloadActive) window.location.reload();
    }, 900);
'@

$new = @'
    // Server merakit file secara async di antrean -- tunggu status 'completed'
    // dulu sebelum reload, supaya file baru langsung muncul di daftar tanpa
    // refresh manual. (Endpoint status ikut menjalankan perakitan sinkron
    // bila antrean lambat, jadi polling ini sekaligus mendorong perakitan.)
    if (callComplete) {
        job.meta = 'Merakit file di server...';
        paint(job);
        const deadline = Date.now() + 15 * 60 * 1000;
        for (;;) {
            await new Promise((resolve) => setTimeout(resolve, 1200));
            if (job.cancelled) return;
            let status = null;
            try { status = await api(`/upload/${job.sessionId}`); } catch { status = null; }
            if (status && status.status === 'completed') break;
            if (status && status.status === 'failed') {
                job.state = 'error';
                job.meta = status.error || 'Perakitan file gagal di server';
                paint(job); notify(job.meta);
                return;
            }
            if (Date.now() > deadline) break; // menyerah menunggu -- tetap tandai selesai
        }
    }

    job.state = 'done';
    job.meta = 'Unggahan selesai';
    paint(job);
    notify(`"${job.name}" berhasil diunggah`);
    setTimeout(() => {
        destroyJob(job);
        // Muat ulang daftar file hanya bila tak ada unggahan lain yang sedang
        // berjalan/dijeda (job gagal/terhenti tidak ikut menghalangi) dan tak
        // ada unduhan terkelola yang akan terputus oleh reload.
        const othersActive = [...jobs.values()].some((j) => j.state === 'uploading' || j.state === 'paused');
        const downloadActive = document.querySelector('[data-action="dl-cancel"]');
        if (!othersActive && !downloadActive) window.location.reload();
    }, 900);
'@

$rel  = 'resources\js\uploader.js'
$path = Join-Path $root $rel
$text = [IO.File]::ReadAllText($path)

if (-not $text.Contains($old)) {
    # Coba varian CRLF bila file di disk memakai ujung baris Windows.
    $oldC = $old -replace "`n", "`r`n"
    $newC = $new -replace "`n", "`r`n"
    if ($text.Contains($oldC)) { $old = $oldC; $new = $newC }
}

if (-not $text.Contains($old)) {
    Write-Host "GAGAL: pola lama tidak ditemukan di $rel -- file mungkin sudah berubah. Kabari Muse." -ForegroundColor Red
    exit 1
}

[IO.File]::WriteAllText($path, $text.Replace($old, $new))
Write-Host "OK: $rel diperbarui." -ForegroundColor Green
Write-Host "Langkah berikutnya: docker compose up -d --build  lalu Ctrl+F5 di browser."
