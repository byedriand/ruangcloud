# RuangCloud di Docker

Panduan menjalankan RuangCloud sebagai container Docker (pengganti/alternatif XAMPP + `php artisan serve`).

## Isi paket

| Layanan | Isi | Alamat |
| --- | --- | --- |
| `web` | Nginx (web server) | http://localhost:8080 |
| `app` | Laravel di PHP-FPM 8.2 | internal (port 9000) |
| `worker` | Queue worker (replikasi file antar node) | — |
| `db` | PostgreSQL 16 | localhost:5433 |
| `redis` | Redis 7 (antrean + cache) | localhost:6380 |
| `minio` | MinIO (penyimpanan objek S3) | API localhost:9000, konsol http://localhost:9001 |
| `minio-init` | Pembuat bucket node (sekali jalan) | — |
| `mailpit` | Mailpit (penangkap email lokal) | Kotak masuk web http://localhost:8025, SMTP localhost:1025 |

Tiap storage node adalah satu **bucket MinIO** yang namanya sama dengan kode node:
`jakarta` (Server 1), `bandung` (Server 2), dan `surabaya` (Server 3). Bucket dibuat
otomatis oleh service `minio-init` saat pertama kali dijalankan; container `app`
menunggu service itu selesai sebelum menerima unggahan. Aplikasi mengakses bucket
lewat API S3 (`StorageNodeService::disk()`), jadi penempatan file, replikasi, dan
failover baca bekerja persis seperti sebelumnya — hanya backend-nya yang berubah.

Email keluar aplikasi (misalnya tautan reset kata sandi, REQ-F-05) tidak benar-benar
terkirim keluar: semuanya tertangkap **Mailpit** dan bisa dibaca di kotak masuk web
http://localhost:8025. Untuk mencoba fitur lupa kata sandi: klik "Lupa kata sandi?"
di halaman masuk, isi email akun, lalu buka Mailpit dan klik tautan di emailnya.

Catatan image: server memakai `pgsty/silo` dan klien init memakai `pgsty/mc`
(fork komunitas MinIO dari proyek Pigsty), karena image resmi `minio/minio` dan
`minio/mc` telah dihapus dari Docker Hub sejak September 2026. API S3, variabel
`MINIO_*`, kredensial, dan konsol web-nya identik dengan MinIO.

## Prasyarat

- Docker Desktop terpasang dan berjalan (status "Engine running").

## Menjalankan

Dari root proyek (folder yang berisi `artisan`):

```powershell
docker compose up -d --build
```

Build pertama butuh beberapa menit (unduh image + `composer install` + build Vite).
Container `app` otomatis menunggu database, menjalankan migrasi, `storage:link`,
dan seed data awal (akun demo + 3 storage node).

Lalu buka http://localhost:8080 dan masuk dengan akun demo:

- Admin: `admin@ruangcloud.id` / `ruangcloud2026`
- User: `naufal@ruangcloud.id` / `ruangcloud2026`

File yang diunggah bisa diintip per node lewat konsol web MinIO di
http://localhost:9001 (masuk: `ruangcloud` / `ruangcloud-secret`) — buka bucket
sesuai kode node untuk melihat objek file beserta salinan replikanya.

## Perintah berguna

```powershell
docker compose ps                 # status semua container (minio-init berstatus Exited (0) = normal)
docker compose logs -f app        # log aplikasi (atau: web, worker, db, minio)
docker compose exec app php artisan tinker
docker compose exec app php artisan nodes:check
docker compose down               # matikan container (data tetap)
docker compose down -v            # matikan + hapus semua data (database & bucket MinIO)
docker compose up -d --build      # bangun ulang setelah ada perubahan kode
```

## Catatan

- Mode Docker ini **terpisah total** dari mode XAMPP: database dan file-file di node
  mulai dari kosong (data XAMPP tidak ikut pindah).
- File `.env` proyek tetap dipakai (di-mount ke container); nilai `DB_*` dan `APP_URL`
  ditimpa oleh `docker-compose.yml` agar menunjuk ke container `db`.
- Port sengaja digeser (web 8080, db 5433, redis 6380) agar tidak bentrok dengan XAMPP/`artisan serve`.
