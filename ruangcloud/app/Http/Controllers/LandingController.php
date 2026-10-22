<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    public function index()
    {
        $features = [
            ['icon' => 'upload',  'title' => 'Unggah tanpa mengulang',   'description' => 'Koneksi terputus bukan masalah. Lanjutkan unggahan tepat dari bagian terakhir.'],
            ['icon' => 'archive', 'title' => 'Data selalu tersedia',     'description' => 'Setiap file direplikasi ke beberapa node agar tetap aman dan mudah diakses.'],
            ['icon' => 'share',   'title' => 'Berbagi lebih terkendali', 'description' => 'Atur kata sandi dan masa berlaku untuk setiap tautan yang kamu bagikan.'],
        ];

        $stats = [
            ['value' => '99,9%',  'label' => 'Ketersediaan'],
            ['value' => '5 GB',   'label' => 'Ukuran per file'],
            ['value' => '3×',     'label' => 'Replikasi data'],
            ['value' => '24 jam', 'label' => 'Sesi upload aktif'],
        ];

        return view('landing', compact('features', 'stats'));
    }
}
