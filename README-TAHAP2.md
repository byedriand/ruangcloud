RuangCloud - Tahap 2: backend sesuai KAK (tanpa Docker)

Isi: login + RBAC (admin/user), database, resumable chunk upload, storage node terdistribusi + replikasi, health check, audit log.

File baru dan file yang MENIMPA file tahap 1
- Menimpa: routes/web.php, resources/js/ruangcloud.js,
  resources/views/user/dashboard.blade.php, resources/views/admin/dashboard.blade.php,
  app/Http/Controllers/AuthController.php, DashboardController.php, AdminController.php
- Baru: semua file lain (config/nodes.php, migrations, models, services, jobs, commands, seeder, uploader.js, dst)

Cara pasang (XAMPP, tanpa Docker)
1. Salin isi folder ini ke root project (pilih replace untuk file yang menimpa).
2. Di .env pastikan:
     APP_TIMEZONE=Asia/Jakarta
     QUEUE_CONNECTION=database
     SESSION_DRIVER=file        (atau database bila tabel sessions sudah ada)
   Nyalakan MySQL di XAMPP dan buat database sesuai DB_DATABASE.
3. Jalankan:
     php artisan migrate
     php artisan db:seed --class=RuangCloudSeeder
   Akun demo (kata sandi semua: ruangcloud2026)
     admin@ruangcloud.id   -> /admin
     naufal@ruangcloud.id  -> /dashboard
4. Jalankan 3 terminal terpisah:
     php artisan serve
     php artisan queue:work --timeout=3600
     npm run dev
   queue:work WAJIB hidup: tanpa itu file berhenti di status "menggabungkan" dan replikasi tidak jalan.
5. Buka http://127.0.0.1:8000 (pakai 127.0.0.1 atau localhost, bukan IP LAN, karena checksum SHA-256 di browser butuh secure context).

Cara kerja singkat
- Browser memecah file jadi chunk 5 MB, hitung SHA-256 tiap chunk, kirim satu per satu ke /upload/{sesi}/chunk.
- Server validasi ukuran + checksum, simpan chunk sementara (storage/app/private/chunks).
- Jika terputus: pilih file yang sama lagi, hanya chunk yang belum diterima yang dikirim (sesi berlaku 24 jam).
- Setelah semua chunk ada, job AssembleUpload menggabungkan, hitung SHA-256 file akhir, simpan ke node dengan ruang terbesar, lalu ReplicateFile menyalin ke node lain (default 2 salinan) dan memverifikasi checksum replika.
- Unduh otomatis pindah ke replika lain bila satu node mati (failover).
- Tiga storage node dibuat sebagai 3 folder lokal: storage/app/nodes/{jakarta,bandung,surabaya}. Untuk uji failover, matikan node dengan: ganti nama salah satu folder, lalu jalankan php artisan nodes:check.

Perintah pemeliharaan
     php artisan nodes:check        health check node, lengkapi replika kurang, ulangi replikasi gagal
     php artisan uploads:cleanup    hapus chunk dari sesi yang lewat 24 jam
   Bisa dijadwalkan lewat routes/console.php (Schedule::command('nodes:check')->everyMinute(); dst) lalu php artisan schedule:work.

Batas yang disengaja / belum ada
- Belum ada: registrasi mandiri, reset kata sandi, tautan berbagi (F-06), pindah/ganti nama file, halaman Pengguna/Audit log penuh di admin. Menu sidebar masih hanya ganti judul.
- Verifikasi integritas: tiap chunk dicek SHA-256 oleh server; checksum file akhir dihitung server dan disimpan, lalu dipakai memverifikasi replika. Browser tidak menghitung hash seluruh file 5 GB.
- Autentikasi memakai sesi + CSRF (same-origin). Bila nanti butuh token untuk klien lain, tambahkan Laravel Sanctum.
- Redis tidak dipakai (di KAK disebut); antrean memakai database queue. Pindah ke Redis cukup ubah QUEUE_CONNECTION dan CACHE_STORE.
- Node kini folder lokal. Untuk server terpisah / MinIO ubah StorageNodeService::disk() ke driver s3.
- PHP: upload_max_filesize dan post_max_size minimal 8M (default XAMPP 40M sudah cukup).
