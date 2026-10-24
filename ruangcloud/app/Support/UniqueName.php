<?php

namespace App\Support;

/**
 * Buat nama unik bergaya Windows Explorer: bila nama sudah dipakai,
 * tambahkan penanda " (1)", " (2)", dst. sebelum ekstensi.
 * Contoh: "laporan.pdf" -> "laporan (1).pdf" -> "laporan (2).pdf".
 */
class UniqueName
{
    /**
     * @param  callable(string): bool  $exists  Pengecek apakah sebuah nama kandidat sudah dipakai.
     */
    public static function resolve(string $name, callable $exists): string
    {
        if (! $exists($name)) {
            return $name;
        }

        // Pisahkan ekstensi dari titik terakhir (nama diawali titik = tanpa ekstensi).
        $dot = strrpos($name, '.');
        if ($dot === false || $dot === 0) {
            $base = $name;
            $ext  = '';
        } else {
            $base = substr($name, 0, $dot);
            $ext  = substr($name, $dot);
        }

        // Bila nama dasar sudah berakhiran " (n)", lanjutkan hitungannya dari sana.
        $counter = 1;
        if (preg_match('/^(.*) \((\d+)\)$/', $base, $m)) {
            $base    = $m[1];
            $counter = ((int) $m[2]) + 1;
        }

        do {
            $candidate = $base . ' (' . $counter . ')' . $ext;
            $counter++;
        } while ($exists($candidate));

        return $candidate;
    }
}
