<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    public function index()
    {
        $features = [
            ['icon' => 'upload',   'title' => 'Unggah tanpa mengulang',    'description' => 'File dipecah menjadi potongan-potongan kecil. Jeda, lanjutkan, atau tutup halamannya — unggahan diteruskan dari titik terakhir, bahkan setelah koneksi terputus.'],
            ['icon' => 'archive',  'title' => 'Terdistribusi di 3 node',   'description' => 'Setiap file disimpan dan direplikasi ke beberapa node penyimpanan. Satu node bermasalah, file-mu tetap bisa diakses dari node lainnya.'],
            ['icon' => 'share',    'title' => 'Berbagi lebih terkendali',  'description' => 'Bagikan tautan dengan kata sandi dan masa berlaku 1, 7, atau 30 hari — dan cabut aksesnya kapan saja kamu mau.'],
            ['icon' => 'folder',   'title' => 'Rapi dengan folder',       'description' => 'Susun file ke dalam folder, temukan lagi dengan pencarian cepat, dan pulihkan file yang tak sengaja terhapus dari tempat sampah.'],
            ['icon' => 'download', 'title' => 'Unduhan terkelola',        'description' => 'Semua unduhan masuk ke satu antrean yang rapi di halaman Download. Unduh banyak file sekaligus tanpa tercecer.'],
            ['icon' => 'activity', 'title' => 'Log aktivitas lengkap',    'description' => 'Setiap unggahan, unduhan, dan tautan yang dibagikan tercatat kronologis di halaman Aktivitas — tidak ada yang luput.'],
        ];

        $stats = [
            ['value' => '5 GB',   'label' => 'Ukuran per file'],
            ['value' => '3',      'label' => 'Node penyimpanan'],
            ['value' => '2×',     'label' => 'Replikasi data'],
            ['value' => '24 jam', 'label' => 'Sesi unggahan aktif'],
        ];

        return view('landing', compact('features', 'stats'));
    }
}
