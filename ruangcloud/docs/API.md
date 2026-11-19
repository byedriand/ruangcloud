# REST API RuangCloud

API berbasis token (Laravel Sanctum) untuk operasi file, folder, dan
unggahan. Seluruh operasi memakai ulang logika aplikasi web: validasi,
hak akses per pengguna, penamaan unik, dan log aktivitas yang sama.

## Autentikasi

Tukar email + kata sandi menjadi token:

```bash
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "naufal@ruangcloud.id", "password": "ruangcloud2026"}'
```

Jawaban: `{ "token": "...", "user": { ... } }`.
Pakai token di semua panggilan berikutnya:

```
Authorization: Bearer <token>
```

## Endpoint

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| POST | `/api/login` | Tukar kredensial menjadi token |
| GET | `/api/me` | Profil pengguna pemilik token |
| POST | `/api/logout` | Cabut token yang sedang dipakai |
| GET | `/api/files` | Daftar file. Filter: `?folder_id=` atau `?q=` |
| GET | `/api/files/{id}` | Detail satu file |
| PATCH | `/api/files/{id}` | Ubah nama (body: `name`) |
| POST | `/api/files/{id}/move` | Pindah folder (body: `folder_id`, boleh null) |
| DELETE | `/api/files/{id}` | Pindah ke sampah (soft delete) |
| GET | `/api/files/{id}/download` | Unduh isi file dari node penyimpanan |
| GET | `/api/folders` | Daftar folder. Filter: `?parent_id=` |
| POST | `/api/folders` | Buat folder (body: `name`, `parent_id` opsional) |
| PATCH | `/api/folders/{id}` | Ubah nama folder |
| POST | `/api/folders/{id}/move` | Pindah folder induk (ada penjaga siklus) |
| DELETE | `/api/folders/{id}` | Hapus folder (harus kosong) |
| POST | `/api/uploads/init` | Buka sesi unggah resumable |
| POST | `/api/uploads/{session}/chunk` | Kirim satu chunk (body: `index`, `checksum`, `chunk`) |
| POST | `/api/uploads/{session}/complete` | Selesaikan sesi, file dirakit worker |
| GET | `/api/uploads/{session}` | Status sesi unggah |
| DELETE | `/api/uploads/{session}` | Batalkan sesi unggah |

Kode status: `401` belum login / token tidak sah, `403` bukan pemilik,
`404` tidak ditemukan, `422` data tidak valid (mis. kuota tidak cukup,
folder tujuan tidak sah, folder belum kosong).
