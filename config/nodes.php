<?php

return [
    // Ukuran chunk (KAK 4.2: default 5 MB, dapat dikonfigurasi).
    'chunk_size' => (int) env('UPLOAD_CHUNK_SIZE', 5 * 1024 * 1024),

    // Batas ukuran file (KAK 2.3: minimal 5 GB).
    'max_file_size' => 5 * 1024 * 1024 * 1024,

    // Sesi unggah dapat dilanjutkan sampai 24 jam (KAK 4.2).
    'session_ttl_hours' => 24,

    // Jumlah salinan tiap file di node berbeda (KAK 2.3: minimal 2).
    'replicas' => (int) env('STORAGE_REPLICAS', 2),
];
