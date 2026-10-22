<?php

namespace App\Console\Commands;

use App\Models\UploadSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UploadsCleanup extends Command
{
    protected $signature = 'uploads:cleanup';
    protected $description = 'Hapus chunk sementara dari sesi unggah yang sudah kedaluwarsa (24 jam)';

    public function handle(): int
    {
        $local = Storage::disk('local');
        $count = 0;

        UploadSession::whereIn('status', ['uploading', 'failed'])
            ->where('expires_at', '<', now())
            ->each(function (UploadSession $session) use ($local, &$count) {
                $local->deleteDirectory($session->chunkDir());
                $local->delete("tmp/{$session->id}.bin");
                $session->update(['status' => 'expired']);
                $count++;
            });

        $this->info("{$count} sesi kedaluwarsa dibersihkan.");

        return self::SUCCESS;
    }
}
