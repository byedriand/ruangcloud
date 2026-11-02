<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\StoredFile;
use App\Support\UniqueName;
use ZipArchive;

/**
 * Bangun arsip ZIP berisi seluruh isi folder (termasuk subfolder).
 * Isi file dibaca dari replika sehat pertama di node penyimpanan.
 */
class FolderArchiveService
{
    public function __construct(private StorageNodeService $nodes)
    {
    }

    /**
     * @return array{0: string, 1: int} [path file ZIP sementara, jumlah file di dalamnya]
     */
    public function build(Folder $folder): array
    {
        $dir = storage_path('app/tmp');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $zipPath = $dir . '/' . uniqid('folder_', true) . '.zip';

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $used = [];
        $count = 0;
        foreach ($folder->archiveEntries() as [$file, $relativePath]) {
            $stream = $this->streamFor($file);
            if ($stream === null) {
                continue;
            }

            $name = UniqueName::resolve($relativePath, fn (string $c) => isset($used[$c]));
            $used[$name] = true;

            $zip->addFromStream($name, $stream);
            fclose($stream);
            $count++;
        }

        $zip->close();

        return [$zipPath, $count];
    }

    /** Stream baca dari replika pertama yang aktif dan benar-benar ada di disk. */
    private function streamFor(StoredFile $file)
    {
        $replicas = $file->replicas()->with('node')->orderByDesc('is_primary')->get();

        foreach ($replicas as $replica) {
            if ($replica->node->status !== 'active') {
                continue;
            }

            $disk = $this->nodes->disk($replica->node);
            if ($disk->exists($file->stored_path)) {
                return $disk->readStream($file->stored_path);
            }
        }

        return null;
    }
}
