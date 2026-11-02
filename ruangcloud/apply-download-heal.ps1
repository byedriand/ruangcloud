# Perbaikan unduh 503: logika pemilihan replika dibuat memulihkan diri.
# Sebab: sekali pemeriksaan exists() meleset (mis. file masih dirakit), replika
# dicap 'failed' PERMANEN di database dan unduhan ditolak selamanya (503),
# padahal file fisiknya ada di node. Kini semua replika diperiksa ke disk,
# yang file-nya ada dipulihkan jadi 'synced' dan langsung dilayani.
# Jalankan dari root proyek:
#   powershell -ExecutionPolicy Bypass -File .\apply-download-heal.ps1
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

$old1 = @'
    /**
     * Respons unduhan dari replika sehat pertama; bila node mati / file hilang pindah ke replika lain
     * (failover). Mengembalikan null bila semua replika tidak dapat diakses.
     */
    public function downloadResponse(StoredFile $file)
    {
        $replicas = $file->replicas()->with('node')
            ->where('status', 'synced')
            ->orderByDesc('is_primary')
            ->get();

        foreach ($replicas as $replica) {
            if ($replica->node->status !== 'active') {
                continue;
            }

            $disk = $this->disk($replica->node);
            if (! $disk->exists($file->stored_path)) {
                $replica->update(['status' => 'failed', 'last_error' => 'File tidak ditemukan di node.']);
                continue;
            }

            return $disk->download($file->stored_path, $file->name, [
                'Content-Type' => $file->mime ?: 'application/octet-stream',
            ]);
        }

        return null;
    }
'@

$new1 = @'
    /**
     * Respons unduhan dari replika sehat pertama; bila node mati / file hilang pindah ke replika lain
     * (failover). Mengembalikan null bila semua replika tidak dapat diakses.
     *
     * Semua replika diperiksa, bukan hanya yang berstatus synced: replika yang
     * file-nya ternyata ada di disk dipulihkan statusnya menjadi synced. Satu
     * pemeriksaan exists() yang meleset (mis. file masih dirakit/direplikasi)
     * tidak lagi menandai replika failed secara permanen.
     */
    public function downloadResponse(StoredFile $file)
    {
        $replicas = $file->replicas()->with('node')
            ->orderByDesc('is_primary')
            ->get();

        foreach ($replicas as $replica) {
            if ($replica->node->status !== 'active') {
                continue;
            }

            $disk = $this->disk($replica->node);
            if (! $disk->exists($file->stored_path)) {
                continue;
            }

            if ($replica->status !== 'synced') {
                $replica->update(['status' => 'synced', 'last_error' => null]);
            }

            return $disk->download($file->stored_path, $file->name, [
                'Content-Type' => $file->mime ?: 'application/octet-stream',
            ]);
        }

        return null;
    }
'@

$old2 = @'
    public function preview(Request $request, StoredFile $file, StorageNodeService $nodes)
    {
        $this->authorizeFile($request, $file);

        $replicas = $file->replicas()->with('node')
            ->where('status', 'synced')
            ->orderByDesc('is_primary')
            ->get();

        foreach ($replicas as $replica) {
            if ($replica->node->status !== 'active') continue;

            $disk = $nodes->disk($replica->node);
            if (! $disk->exists($file->stored_path)) continue;


'@

$new2 = @'
    public function preview(Request $request, StoredFile $file, StorageNodeService $nodes)
    {
        $this->authorizeFile($request, $file);

        $replicas = $file->replicas()->with('node')
            ->orderByDesc('is_primary')
            ->get();

        foreach ($replicas as $replica) {
            if ($replica->node->status !== 'active') continue;

            $disk = $nodes->disk($replica->node);
            if (! $disk->exists($file->stored_path)) continue;

            if ($replica->status !== 'synced') {
                $replica->update(['status' => 'synced', 'last_error' => null]);
            }


'@

Replace-Exact 'app\Services\StorageNodeService.php' $old1 $new1
Replace-Exact 'app\Http\Controllers\FileController.php' $old2 $new2
Write-Host "Langkah berikutnya: docker compose up -d --build"
