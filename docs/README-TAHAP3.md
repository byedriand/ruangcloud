RuangCloud - Tahap 3: halaman pengguna lengkap

Tiap menu sidebar sekarang punya halaman sendiri:
  /dashboard  Beranda
  /files      File saya (folder bisa dibuka, subfolder, breadcrumb, pencarian, unggah ke folder aktif)
  /shared     Dibagikan (daftar tautan berbagi, salin, cabut)
  /recent     Terbaru (50 file terakhir)
  /activity   Aktivitas (riwayat akun sendiri)
  /trash      Sampah (pulihkan, hapus permanen, kosongkan)
Halaman publik tautan berbagi: /s/{token} (kata sandi opsional, masa berlaku, bisa dicabut).

Aksi pada file (menu titik tiga): unduh, bagikan, ubah nama, pindahkan ke folder, pindahkan ke sampah.
Aksi pada folder: buat (di dalam folder aktif), ubah nama, hapus (hanya bila kosong).

Cara pasang
1. Salin isi folder ini ke root project, pilih replace untuk file yang sudah ada.
2. php artisan migrate        (2 migrasi baru: soft delete file + tabel share_links; tidak perlu seed ulang)
3. php artisan optimize:clear
4. Pastikan npm run dev, php artisan serve, dan php artisan queue:work masih berjalan.

File yang menimpa file sebelumnya
  routes/web.php, resources/js/ruangcloud.js, resources/js/uploader.js,
  resources/views/user/dashboard.blade.php, resources/views/components/{icon,folder-card}.blade.php,
  app/Http/Controllers/{DashboardController,FileController,FolderController,UploadController,AdminController}.php,
  app/Models/{StoredFile,Folder}.php, app/Services/StorageNodeService.php, app/Support/Fmt.php
Semua file lain baru.

Aturan yang dipakai
- Menghapus file = pindah ke Sampah. File di Sampah tetap memakai kuota sampai dihapus permanen.
- Hapus permanen membuang file dari semua node dan mencabut tautan berbagi miliknya.
- Tautan berbagi berhenti berfungsi bila dicabut, kedaluwarsa, atau file masuk Sampah.
- Sidebar menampilkan peringatan saat kuota terpakai di atas 85%.

Belum ada
- Pratinjau file, pindah/ubah nama folder ke folder lain, berbagi folder.
- Sampah belum dikosongkan otomatis setelah N hari.
- Halaman Pengguna / Audit log penuh di sisi admin.
