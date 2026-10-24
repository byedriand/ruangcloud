<?php

namespace App\Console\Commands;

use App\Models\StorageNode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Bersihkan semua data uji (file, folder, audit log, sesi unggah, antrean),
 * TANPA menghapus data akun pengguna dan konfigurasi storage node.
 *
 * Pemakaian:  php artisan data:clean
 *             php artisan data:clean --force   (tanpa pertanyaan konfirmasi)
 */
class CleanData extends Command
{
    protected $signature = 'data:clean {--force : Jalankan tanpa konfirmasi}';
    protected $description = 'Hapus semua data file & aktivitas, kecuali akun pengguna dan storage node';

    /** Tabel data yang dibersihkan (urutan anak -> induk, aman untuk foreign key). */
    private array $tables = [
        'share_links',
        'file_replicas',
        'files',
        'folders',
        'upload_sessions',
        'activity_logs',
        'jobs',
        'job_batches',
        'failed_jobs',
    ];

    public function handle(): int
    {
        $usersKept = DB::table('users')->count();
        $filesCount = DB::table('files')->count();
        $logsCount = DB::table('activity_logs')->count();

        $this->warn('PERINGATAN: semua data berikut akan dihapus PERMANEN:');
        $this->line("  - {$filesCount} file beserta folder, share link, dan replikanya (database + fisik di node)");
        $this->line("  - {$logsCount} baris audit log");
        $this->line('  - Semua sesi unggah, chunk sisa, dan antrean job');
        $this->info("Yang DIPERTAHANKAN: {$usersKept} akun pengguna dan konfigurasi storage node (Server 1/2/3).");

        if (! $this->option('force') && ! $this->confirm('Lanjutkan pembersihan?', false)) {
            $this->line('Dibatalkan, tidak ada data yang dihapus.');

            return self::SUCCESS;
        }

        // 1) Bersihkan tabel data.
        Schema::disableForeignKeyConstraints();
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("Tabel {$table} dikosongkan.");
            }
        }
        Schema::enableForeignKeyConstraints();

        // 2) Hapus file fisik di setiap storage node (folder node-nya sendiri dipertahankan).
        foreach (StorageNode::all() as $node) {
            if ($node->path && str_starts_with($node->path, storage_path('app')) && File::isDirectory($node->path)) {
                File::cleanDirectory($node->path);
                $this->line("File fisik di node {$node->name} dihapus.");
            }
        }

        // 3) Hapus sisa chunk & file sementara unggahan.
        foreach (['chunks', 'tmp'] as $dir) {
            $path = storage_path('app/private/' . $dir);
            if (File::isDirectory($path)) {
                File::cleanDirectory($path);
            }
        }
        $this->line('Sisa chunk dan file sementara dihapus.');

        // 4) Health check ulang agar status node kembali aktif & file .health dibuat ulang.
        $this->call('nodes:check');

        $this->info('Selesai. Semua data bersih, akun pengguna tetap utuh. Silakan refresh Admin Console.');

        return self::SUCCESS;
    }
}
