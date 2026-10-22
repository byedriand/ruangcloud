<?php

namespace App\Support;

use App\Models\StoredFile;
use Carbon\Carbon;

class Fmt
{
    public static function bytes(int|float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        $v = (float) $bytes;
        while ($v >= 1024 && $i < count($units) - 1) {
            $v /= 1024;
            $i++;
        }

        return ($i === 0 ? number_format($v, 0, ',', '.') : number_format($v, 1, ',', '.')) . ' ' . $units[$i];
    }

    /** Ekstensi file dari nama atau fallback MIME. */
    public static function extension(string $name, ?string $mime = null): string
    {
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if ($ext !== '') {
            return $ext;
        }

        $mime = strtolower((string) $mime);
        return match (true) {
            $mime === 'image/png'                                               => 'png',
            in_array($mime, ['image/jpeg', 'image/jpg'])                        => 'jpg',
            $mime === 'image/webp'                                              => 'webp',
            $mime === 'image/gif'                                               => 'gif',
            $mime === 'image/svg+xml'                                           => 'svg',
            $mime === 'application/pdf'                                         => 'pdf',
            str_contains($mime, 'wordprocessingml') || $mime === 'application/msword' => 'docx',
            str_contains($mime, 'spreadsheetml') || $mime === 'application/vnd.ms-excel' => 'xlsx',
            str_contains($mime, 'presentationml') || $mime === 'application/vnd.ms-powerpoint' => 'pptx',
            $mime === 'text/plain'                                              => 'txt',
            $mime === 'text/csv'                                                => 'csv',
            $mime === 'application/zip'                                         => 'zip',
            $mime === 'video/mp4'                                               => 'mp4',
            in_array($mime, ['audio/mpeg', 'audio/mp3'])                        => 'mp3',
            default                                                             => '',
        };
    }

    /** [tipe, ikon, warna, ekstensi] untuk tampilan daftar file. */
    public static function meta(string $name, ?string $mime): array
    {
        $ext  = self::extension($name, $mime);
        $mime = (string) $mime;

        $type = match (true) {
            $ext === 'pdf'                                  => 'Dokumen PDF',
            in_array($ext, ['doc', 'docx'])                 => 'Dokumen Word' . ($ext ? ' (' . strtoupper($ext) . ')' : ''),
            in_array($ext, ['xls', 'xlsx'])                 => 'Spreadsheet Excel' . ($ext ? ' (' . strtoupper($ext) . ')' : ''),
            $ext === 'csv'                                  => 'Data CSV',
            in_array($ext, ['ppt', 'pptx'])                 => 'Presentasi' . ($ext ? ' (' . strtoupper($ext) . ')' : ''),
            in_array($ext, ['txt', 'md'])                   => 'Dokumen Teks',
            in_array($ext, ['jpg', 'jpeg'])                 => 'Foto JPG',
            $ext === 'png'                                  => 'Foto PNG',
            $ext === 'webp'                                 => 'Foto WEBP',
            $ext === 'gif'                                  => 'Gambar GIF',
            $ext === 'svg'                                  => 'Gambar SVG',
            str_starts_with($mime, 'image/')                => 'Foto ' . ($ext ? strtoupper($ext) : 'Gambar'),
            in_array($ext, ['mp4', 'mov', 'mkv', 'avi', 'webm'])
                || str_starts_with($mime, 'video/')         => 'Video ' . ($ext ? strtoupper($ext) : ''),
            in_array($ext, ['mp3', 'wav', 'ogg', 'flac'])
                || str_starts_with($mime, 'audio/')         => 'Audio ' . ($ext ? strtoupper($ext) : ''),
            in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])=> 'Arsip ' . ($ext ? strtoupper($ext) : 'ZIP'),
            default                                         => $ext ? 'Dokumen ' . strtoupper($ext) : 'File',
        };

        $icon = match (true) {
            $ext === 'pdf'                                                     => 'file',
            in_array($ext, ['ppt', 'pptx'])                                    => 'archive',
            in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])
                || str_starts_with($mime, 'image/')                            => 'image',
            in_array($ext, ['xls', 'xlsx', 'csv'])                             => 'file',
            in_array($ext, ['mp4', 'mov', 'mkv', 'avi', 'webm'])
                || str_starts_with($mime, 'video/')                            => 'video',
            in_array($ext, ['mp3', 'wav', 'ogg', 'flac'])
                || str_starts_with($mime, 'audio/')                            => 'music',
            in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])                  => 'archive',
            in_array($ext, ['doc', 'docx', 'txt', 'md'])                       => 'file',
            default                                                            => 'file',
        };

        $color = match (true) {
            $ext === 'pdf'                                                     => 'red',
            in_array($ext, ['ppt', 'pptx'])                                    => 'orange',
            in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])
                || str_starts_with($mime, 'image/')                            => 'purple',
            in_array($ext, ['xls', 'xlsx', 'csv'])                             => 'green',
            in_array($ext, ['mp4', 'mov', 'mkv', 'avi', 'webm'])
                || str_starts_with($mime, 'video/')                            => 'blue',
            in_array($ext, ['mp3', 'wav', 'ogg', 'flac'])
                || str_starts_with($mime, 'audio/')                            => 'purple',
            in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])                  => 'orange',
            in_array($ext, ['doc', 'docx', 'txt', 'md'])                       => 'blue',
            default                                                            => 'blue',
        };

        return [trim($type), $icon, $color, $ext];
    }

    public static function date(Carbon $date): string
    {
        $tz = config('app.timezone', 'Asia/Jakarta');
        $d = $date->copy()->timezone($tz)->locale('id');

        if ($d->isToday()) {
            return 'Hari ini, ' . $d->format('H.i');
        }
        if ($d->isYesterday()) {
            return 'Kemarin, ' . $d->format('H.i');
        }

        return $d->translatedFormat('j F Y');
    }

    public static function initials(string $name): string
    {
        return collect(preg_split('/\s+/', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->implode('');
    }

    /** Baris file siap tampil untuk tabel file. */
    public static function fileRow(StoredFile $file): array
    {
        [$type, $icon, $color, $ext] = self::meta($file->name, $file->mime);

        $baseName = pathinfo($file->name, PATHINFO_FILENAME);
        if ($ext && strtolower(pathinfo($file->name, PATHINFO_EXTENSION)) !== strtolower($ext)) {
            $baseName = $file->name;
        }

        $isUpdated = $file->updated_at && $file->created_at && $file->updated_at->gt($file->created_at->copy()->addSeconds(5));
        $updatedDate = $isUpdated ? self::date($file->updated_at) : null;

        return [
            'id'          => $file->id,
            'name'        => $file->name,
            'baseName'    => $baseName,
            'extension'   => $ext,
            'type'        => $type,
            'size'        => self::bytes($file->size),
            'date'        => self::date($file->created_at),
            'isUpdated'   => $isUpdated,
            'updatedDate' => $updatedDate,
            'icon'        => $icon,
            'color'       => $color,
            'mime'        => $file->mime ?? '',
            'previewUrl'  => route('files.preview', $file->id),
            'downloadUrl' => route('files.download', $file->id),
        ];
    }

    public static function actionIcon(string $action): string
    {
        return [
            'upload' => 'upload', 'login' => 'users', 'download' => 'download', 'delete' => 'trash',
            'replicate' => 'archive', 'folder' => 'folder', 'share' => 'link', 'restore' => 'restore',
        ][$action] ?? 'activity';
    }
}
