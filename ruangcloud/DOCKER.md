# RuangCloud di Docker

Panduan menjalankan RuangCloud sebagai container Docker (pengganti/alternatif XAMPP + `php artisan serve`).

## Isi paket

| Layanan | Isi | Alamat |
| --- | --- | --- |
| `web` | Nginx (web server) | http://localhost:8080 |
| `app` | Laravel di PHP-FPM 8.2 | internal (port 9000) |
| `worker` | Queue worker (replikasi file antar node) | — |
| `db` | MariaDB 11 | localhost:3307 |
| `redis` | Redis 7 (antrean + cache) | localhost:6380 |
| `node-jakarta` | Node penyimpanan Server 1 | http://localhost:8081 |
| `node-bandung` | Node penyimpanan Server 2 | http://localhost:8082 |
| `node-surabaya` | Node penyimpanan Server 3 | http://localhost:8083 |

Tiap node adalah container Nginx yang menyajikan volumenya sendiri. Volume yang sama juga
dipasang ke container `app` di path yang selama ini dipakai aplikasi
(`storage/app/nodes/<kode>`), jadi tidak ada kode aplikasi yang berubah: aplikasi menulis
ke folder node = menulis ke volume container node tersebut.

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

File yang diunggah bisa diintip per node lewat alamat node di atas (daftar isi volume).

## Perintah berguna

```powershell
docker compose ps                 # status semua container
docker compose logs -f app        # log aplikasi (atau: web, worker, db, node-jakarta)
docker compose exec app php artisan tinker
docker compose exec app php artisan nodes:check
docker compose down               # matikan container (data tetap)
docker compose down -v            # matikan + hapus semua data (database & file node)
docker compose up -d --build      # bangun ulang setelah ada perubahan kode
```

## Catatan

- Mode Docker ini **terpisah total** dari mode XAMPP: database dan file-file di node
  mulai dari kosong (data XAMPP tidak ikut pindah).
- File `.env` proyek tetap dipakai (di-mount ke container); nilai `DB_*` dan `APP_URL`
  ditimpa oleh `docker-compose.yml` agar menunjuk ke container `db`.
- Port sengaja digeser (web 8080, db 3307) agar tidak bentrok dengan XAMPP/`artisan serve`.
