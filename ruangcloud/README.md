# RuangCloud

> **Perancangan dan Pembangunan Sistem Informasi Penyimpanan Cloud Terdistribusi Berbasis Web Menggunakan Metode Resumable Chunk Upload**  
> *Usulan Teknis (USTEK) - Kelompok 4 | Fakultas Ilmu Komputer dan Sistem Informasi (FIKSI), Universitas Kebangsaan Republik Indonesia (UKRI), Bandung (2026)*

---

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![MariaDB](https://img.shields.io/badge/MariaDB-11-003545?style=for-the-badge&logo=mariadb&logoColor=white)](https://mariadb.org)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com)
[![License](https://img.shields.io/badge/License-Academic%20%2F%20MIT-green?style=for-the-badge)](#lisensi)

---

## 📌 Daftar Isi
1. [Tentang Proyek](#-tentang-proyek)
2. [Tim Pengembang](#-tim-pengembang)
3. [Latar Belakang & Identifikasi Masalah](#-latar-belakang--identifikasi-masalah)
4. [Ruang Lingkup & Sasaran Keberhasilan](#-ruang-lingkup--sasaran-keberhasilan)
5. [Arsitektur Sistem (4 Lapisan)](#-arsitektur-sistem-4-lapisan)
6. [Fitur Utama & Pemenuhan Kebutuhan Fungsional](#-fitur-utama--pemenuhan-kebutuhan-fungsional)
7. [Mekanisme Inti Sistem](#-mekanisme-inti-sistem)
8. [Tumpukan Teknologi (Tech Stack)](#-tumpukan-teknologi-tech-stack)
9. [Menjalankan dengan Docker (Cara Utama)](#-menjalankan-dengan-docker-cara-utama)
10. [Menjalankan Manual / Pengembangan Lokal](#-menjalankan-manual--pengembangan-lokal)
11. [Akun Demo Pengujian](#-akun-demo-pengujian)
12. [Perintah Khusus Pemeliharaan (CLI)](#-perintah-khusus-pemeliharaan-cli)
13. [Struktur Direktori Proyek](#-struktur-direktori-proyek)
14. [Rencana Pengujian & Kriteria Kelulusan](#-rencana-pengujian--kriteria-kelulusan)
15. [Dasar Hukum & Standar Teknis](#-dasar-hukum--standar-teknis)

---

## 📖 Tentang Proyek

**RuangCloud** adalah sistem informasi penyimpanan awan (cloud storage) mandiri yang dirancang untuk mengatasi keterbatasan unggah file berukuran besar dan mengeliminasi *Single Point of Failure* (SPOF) pada peladen tunggal.

Sistem ini mengimplementasikan metode **Resumable Chunk Upload** (pemotongan berkas di sisi peramban klien dengan verifikasi checksum per chunk) serta **Penyimpanan Terdistribusi Multi-Node** dengan replikasi asinkron otomatis dan mekanisme failover baca dari replika yang sehat.

Seluruh aplikasi kini berjalan di atas **Docker** (7 container: web, aplikasi, worker, database, dan 3 node penyimpanan) sehingga dapat dijalankan konsisten di mesin mana pun tanpa konfigurasi server manual.

---

## 👥 Tim Pengembang

Disusun dan dikembangkan oleh **Kelompok 4**  
**Program Studi Sistem Informasi**  
**Fakultas Ilmu Komputer dan Sistem Informasi (FIKSI)**  
**Universitas Kebangsaan Republik Indonesia (UKRI)**  
*Jl. Terusan Halimun No. 37, Lengkong, Kota Bandung, Jawa Barat 40263*

| No | Nama Anggota | NPM | Peran dalam Tim | Tanggung Jawab Utama |
|:--:|:---|:---:|:---|:---|
| 1 | **Nayla Rabia Gustari** | `20241320034` | **Project Manager** | Perencanaan, pengendalian jadwal & risiko, pelaporan, koordinasi klien. |
| 2 | **Aisma Haidy Putri Berry Ani N.R** | `20241320001` | **System Analyst & UI/UX** | SRS, ERD, UML, prototipe antarmuka, validasi kebutuhan pengguna. |
| 3 | **Adrian Ronald Daga** | `20241320011` | **Frontend / Backend Developer** | REST API Laravel, chunk upload logic, arsitektur replikasi node, UI web. |
| 4 | **Fakhry Ahmad Fauzan** | `20241320038` | **Frontend / Backend Developer** | File management, queue processing, integrasi antarmuka klien. |
| 5 | **Arya Adi Muhammad Iqbal** | `20241320018` | **QA Tester** | Pengujian fungsional, resume testing, load testing, failover, & UAT. |

**Disetujui oleh Perwakilan Klien:** Sona Mardiana

---

## 🎯 Latar Belakang & Identifikasi Masalah

Berdasarkan Kerangka Acuan Kerja (KAK) dan dokumen Usulan Teknis (USTEK), terdapat 3 tantangan utama dalam penyimpanan berkas digital:

1. **Kegagalan Unggah File Berukuran Besar**: Unggah berkas berkapasitas besar (hingga 5 GB) seringkali mengalami kegagalan akibat timeout koneksi HTTP atau fluktuasi jaringan, mengharuskan pengguna mengulang proses dari 0%.
2. **Single Point of Failure (SPOF)**: Penyimpanan yang hanya bertumpu pada satu peladen fisik berisiko tinggi terhadap kehilangan data permanen dan terhentinya operasional layanan saat peladen mengalami kerusakan (*down*).
3. **Ketiadaan Tata Kelola Terpusat**: Belum adanya manajemen hak akses berbasis peran (RBAC), batas kuota penyimpanan, pembagian tautan terkontrol, dan jejak audit (*audit trail*) menyeluruh yang dapat dipertanggungjawabkan.

---

## 📊 Ruang Lingkup & Sasaran Keberhasilan

| No | Sasaran KAK / USTEK | Indikator Keberhasilan | Pendekatan Solusi RuangCloud |
|:--:|:---|:---|:---|
| 1 | **Unggah File Besar Andal** | File hingga 5 GB berhasil diunggah & dapat dilanjutkan dari chunk terakhir. | Client-side chunking (5 MB), status sesi di server, resume hingga 24 jam. |
| 2 | **Ketersediaan Data Tinggi (HA)** | Minimal 2 replika pada node berbeda; berkas tetap terunduh saat 1 node bermasalah. | Replikasi asinkron via Laravel Queue, failover baca ke replika sehat, status replika self-healing. |
| 3 | **Integritas Data Mutlak** | Verifikasi Checksum SHA-256 berhasil 100% pada berkas hasil penggabungan. | Validasi SHA-256 per chunk dan checksum total akhir saat penggabungan. |
| 4 | **Kinerja Memadai** | Menangani beban minimal 100 pengguna aktif bersamaan. | Antrean asinkron (queue worker), penggabungan berkas berbasis stream. |
| 5 | **Tingkat Kebergunaan** | Tingkat penerimaan pengujian pengguna (UAT) minimal 85%. | Antarmuka kaca (glassmorphism) responsif, progress bar interaktif, dialog bertema menggantikan popup bawaan browser. |

---

## 🏗 Arsitektur Sistem (4 Lapisan)

Sistem RuangCloud dirancang dengan arsitektur 4 lapisan modular (*four-tier decoupled architecture*):

```
┌────────────────────────────────────────────────────────────────────────┐
│                        LAPISAN KLIEN (FRONTEND)                        │
│    Modern Web Browsers (Chrome, Firefox, Safari, Edge)                 │
│    • HTML5 File API (Blob Slicing & SHA-256 Crypto API)                │
│    • Responsive UI (Tailwind CSS v4 + Vanilla JS Component System)     │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ HTTP / RESTful API (Chunk & Auth)
┌───────────────────────────────────▼────────────────────────────────────┐
│                       LAPISAN APLIKASI (BACKEND)                       │
│    Laravel 12 REST API Framework (PHP-FPM, container Docker)           │
│    • Session & Chunk Ingestion Coordinator                             │
│    • Role-Based Access Control (RBAC) & Quota Enforcement              │
│    • Background Queue Workers (AssembleUpload & ReplicateFile)         │
└──────────────────┬─────────────────────────────────┬───────────────────┘
                   │                                 │
                   │ Query & Transaction             │ Read / Write / Health
┌──────────────────▼───────────────┐ ┌───────────────▼───────────────────┐
│       LAPISAN DATA (DATABASE)     │ │  LAPISAN PENYIMPANAN TERDISTRIBUSI │
│  MariaDB Database:               │ │  Cluster Storage Nodes (3 Node)    │
│  • Metadata File & Folder        │ │  • Node Jakarta (Primary / Replica)│
│  • Status Sesi Chunk & Upload    │ │  • Node Bandung (Primary / Replica)│
│  • RBAC, Kuota, Share Links      │ │  • Node Surabaya (Primary/Replica) │
│  • Audit Trail & Activity Logs   │ │  (Ditampilkan sebagai Server 1/2/3)│
│  • Riwayat Unduhan Selesai       │ │                                    │
└──────────────────────────────────┘ └───────────────────────────────────┘
```

---

## 🚀 Fitur Utama & Pemenuhan Kebutuhan Fungsional

Sistem mengimplementasikan seluruh kebutuhan fungsional modul **F-01 hingga F-09**:

- **F-01: Autentikasi dan Otorisasi (RBAC)**
  Autentikasi aman dengan *Role-Based Access Control* yang memisahkan hak akses **Administrator** dan **Pengguna**. Password ter-hash bcrypt, proteksi CSRF, indikator kekuatan kata sandi, tombol intip sandi, dan alur registrasi yang mengarahkan pengguna ke halaman login (tidak auto-login) dengan popup sukses.
- **F-02: Manajemen File dan Folder Lengkap**
  Folder bertingkat (*nested folders*) dengan breadcrumb, pembuatan/rename/hapus folder, pencarian cepat, pratinjau berkas, dan tempat sampah (*soft delete*). Setiap file dan folder memiliki **menu titik tiga** berisi aksi kontekstual; nama yang bentrok otomatis diberi penanda ` (1)`, ` (2)` ala Windows. File maupun folder dapat **diseret (drag & drop)** untuk dipindahkan ke folder lain atau ke breadcrumb. Kartu folder menampilkan foto/nama pemilik dan ukuran total isinya.
- **F-03: Resumable Chunk Upload (Anti-Gagal)**
  Berkas hingga **5 GB** dipecah di sisi klien menjadi chunk **5 MB** dan diunggah berurutan. Mendukung jeda (*pause*), lanjutkan (*resume*), dan batal (*cancel*), termasuk resume lintas sesi dari penyimpanan lokal peramban. Sesi unggah bertahan 24 jam (sliding) dan dapat dilanjutkan dari chunk terakhir tanpa mengulang.
- **F-04: Progress dan Status Real-Time**
  Baris transfer aktif menampilkan progress bar, persentase, kecepatan, estimasi sisa waktu, dan ukuran — untuk unggahan maupun unduhan.
- **F-05: Penyimpanan Terdistribusi & Replikasi Asinkron**
  File ditempatkan pada node primer berkapasitas bebas terbesar, lalu direplikasi asinkron oleh queue worker hingga **2 salinan fisik di node berbeda**. Unduhan dan pratinjau bersifat *self-healing*: sistem memeriksa semua replika ke disk, menyajikan dari replika yang sehat, dan memulihkan status replika yang terbukti ada.
- **F-06: Berbagi Terkontrol (Secure Share Link)**
  Tautan publik `/s/{token}` untuk **file maupun folder**, dengan kata sandi opsional dan masa berlaku **1 / 7 / 30 hari**, dapat dicabut sewaktu-waktu. Halaman publik folder menampilkan pemilik ("Dibagikan oleh"), daftar isi, unduh per file, dan unduh semua sebagai ZIP. Folder juga dapat diunduh sebagai ZIP oleh pemiliknya.
- **Manajer Unduhan & Riwayat Satu Kali Unduh**
  Klik *Unduh berkas* membawa pengguna ke halaman Download dan progres unduhan berjalan di sana. File yang **sudah selesai diunduh tercatat di basis data dan tidak dapat diunduh lagi**; riwayat dapat dihapus lewat menu titik tiga (menghapus riwayat membuka kembali izin unduh).
- **F-07: Manajemen Kuota Penyimpanan Pengguna**
  Kuota per pengguna (default **5 GB**, administrator 1.000 GB) dengan widget pemakaian di sidebar (termasuk berkas di sampah) dan halaman **Detail Penyimpanan** (`/penyimpanan`): rincian pemakaian per jenis file (8 kategori), per node (Server 1/2/3), daftar file terbesar, dan ringkasan sampah.
- **Profil Pengguna**
  Pengguna dapat mengubah nama, email, foto profil (avatar), dan kata sandi; setiap perubahan profil tercatat di Log Aktivitas.
- **F-08: Admin Console**
  Satu halaman konsol dengan panel **Ringkasan, Pengguna, Penyimpanan, Unggahan, dan Audit Log**. Admin dapat melihat detail pengguna, menjadikan/mencabut peran admin (dengan penjaga admin terakhir), dan mengatur kuota per pengguna. Nama berkas pengguna ditampilkan teranonimkan pada panel admin demi privasi, dan halaman Profil admin berada di dalam konsol.
- **F-09: Log Aktivitas & Jejak Audit (Audit Trail)**
  Pencatatan aksi penting sistem (login, unggah, unduh, hapus, pulihkan, bagikan, ganti nama, perubahan profil, aksi admin) lengkap dengan waktu dan identitas aktor.

---

## ⚙️ Mekanisme Inti Sistem

### 1. Alur Resumable Chunk Upload
```
[User Browser]                              [Laravel Server]                    [Storage Node]
      │                                             │                                  │
      ├── 1. Baca File & Hitung SHA-256 Chunk ────>│                                  │
      ├── 2. POST /upload/initiate ───────────────>│ Buat Sesi Upload                  │
      │<─── Balas Upload ID & Next Chunk Index ─────┤ (Status: active, TTL: 24h)       │
      │                                             │                                  │
      ├── 3. PUT /upload/{id}/chunk (Chunk #N) ───>│ Verifikasi Ukuran & SHA-256      │
      │<─── HTTP 200 OK (Chunk Tersimpan) ──────────┤ Simpan ke storage/chunks/        │
      │                                             │                                  │
      │ (Ulangi untuk seluruh chunk... Jika koneksi │                                  │
      │  terputus, query status & lanjut chunk-N)   │                                  │
      │                                             │                                  │
      ├── 4. POST /upload/{id}/complete ───────────>│ Dispatch Job AssembleUpload      │
      │                                             ├─── 5. Gabungkan File (Stream) ──>│
      │                                             ├─── 6. Verifikasi Final SHA-256   │
      │                                             ├─── 7. Hapus Chunk Sementara      │
      │                                             └─── 8. Dispatch Job Replicate ───>│ (Replika ke Node Lain)
```

### 2. Penyimpanan Terdistribusi & Replikasi
- **Penempatan Node Primer**: Saat penggabungan berkas selesai, sistem memilih node aktif dengan ruang bebas terbesar sebagai node primer.
- **Faktor Replikasi**: File disalin ke node lain hingga terpenuhi target **2 salinan fisik** di node berbeda (status replika: `pending` → `synced`/`failed`).
- **Verifikasi Integritas**: Checksum SHA-256 dihitung pada file hasil gabungan di node primer sebelum replika dinyatakan sah.

### 3. Failover Baca & Self-Healing
- Jika node primer bermasalah, pengunduhan dan pratinjau berkas dialihkan secara transparan ke replika yang sehat (*transparent read failover*) tanpa downtime bagi pengguna.
- Pemeriksaan replika bersifat *self-healing*: replika yang fisiknya ditemukan di disk dipulihkan statusnya menjadi `synced`, dan satu kegagalan baca tidak lagi mencap replika sebagai gagal permanen.
- Perintah artisan `nodes:check` memeriksa kesehatan folder node dan menjadwalkan ulang replikasi yang kurang dari target.

---

## 🛠 Tumpukan Teknologi (Tech Stack)

| Komponen | Teknologi | Keterangan |
|:---|:---|:---|
| **Backend Framework** | Laravel 12.x (PHP 8.2+) | RESTful API, Blade Components, Queue Jobs, Eloquent ORM |
| **Frontend UI** | Vanilla JS + Tailwind CSS v4 + Vite 7 | Tema kaca (glassmorphism), responsif, tanpa framework JS berat |
| **Database** | MariaDB 11 | Transaksional, foreign key berindeks, UTF8MB4 |
| **Queue / Asinkron** | Laravel Database Queue | Penggabungan chunk & replikasi antar node oleh worker khusus |
| **Penyimpanan Node** | Local Storage Multi-Directory | Simulasi cluster 3 node (Jakarta, Bandung, Surabaya) |
| **Kontainerisasi** | Docker & Docker Compose | 7 service: web (nginx), app (PHP-FPM), worker, db, 3 node |
| **Keamanan & Standar** | SHA-256, Bcrypt, PSR-12, OWASP | Validasi MIME, integritas hash kriptografis, proteksi CSRF |

---

## 🐳 Menjalankan dengan Docker (Cara Utama)

Cara resmi dan yang direkomendasikan — tidak perlu XAMPP, PHP, atau Node.js di mesin host.

### Prasyarat
- **Docker Desktop** (Windows/macOS) atau Docker Engine + Compose plugin (Linux).

### Langkah
```bash
git clone https://github.com/byedriand/ruangcloud.git
cd ruangcloud/ruangcloud
docker compose up -d --build
```

Tunggu hingga semua container berstatus *Started/Healthy*. Migrasi database dan seeding data awal (**RuangCloudSeeder**: akun demo + 3 node penyimpanan) berjalan **otomatis** saat container aplikasi pertama kali menyala.

Buka aplikasi di: **`http://localhost:8080`**

### Peta Layanan & Port

| Service | Port Host | Keterangan |
|:---|:---:|:---|
| `web` | **8080** | nginx → aplikasi RuangCloud |
| `db` | 3307 | MariaDB 11 (database `ruangcloud`) |
| `node-jakarta` | 8081 | Server 1 — node penyimpanan Jakarta |
| `node-bandung` | 8082 | Server 2 — node penyimpanan Bandung |
| `node-surabaya` | 8083 | Server 3 — node penyimpanan Surabaya |
| `app` | — | PHP-FPM (internal) |
| `worker` | — | Queue worker: perakitan chunk & replikasi |

### Perintah Docker yang Sering Dipakai
```bash
docker compose up -d --build   # bangun ulang setelah ada perubahan kode
docker compose ps              # status seluruh container
docker compose logs -f app     # pantau log aplikasi
docker compose down            # hentikan semua container (data tetap tersimpan di volume)
```

Panduan Docker lebih rinci tersedia di berkas `DOCKER.md` pada direktori aplikasi.

---

## 💻 Menjalankan Manual / Pengembangan Lokal

Alternatif untuk pengembangan tanpa Docker (misalnya dengan XAMPP):

### Prasyarat
- PHP 8.2+ (ekstensi: `fileinfo`, `pdo_mysql`, `mbstring`, `openssl`, `zip`), Composer 2.x, Node.js 18+ & npm, MariaDB/MySQL.
- `php.ini`: `upload_max_filesize` dan `post_max_size` harus lebih besar dari ukuran chunk 5 MB (pada paket Docker nilai ini sudah diatur 64M/72M).

### Langkah
```bash
cd ruangcloud/ruangcloud
composer install
npm install
cp .env.example .env
php artisan key:generate
# sesuaikan DB_* di .env, buat database "ruangcloud", lalu:
php artisan migrate
php artisan db:seed --class=RuangCloudSeeder
```

### Tiga proses yang harus berjalan
```bash
php artisan serve                 # Terminal 1 — web server
php artisan queue:work            # Terminal 2 — worker (wajib: perakitan & replikasi)
npm run dev                       # Terminal 3 — Vite (atau: npm run build)
```
Buka **`http://127.0.0.1:8000`**.

---

## 🔑 Akun Demo Pengujian

Seluruh akun demo memiliki kata sandi default: **`ruangcloud2026`**

| Role | Email | Password | Kuota | Akses Halaman |
|:---|:---|:---:|:---:|:---|
| **Administrator** | `admin@ruangcloud.id` | `ruangcloud2026` | 1.000 GB | Admin Console (Ringkasan, Pengguna, Penyimpanan, Unggahan, Audit Log) |
| **Pengguna** | `naufal@ruangcloud.id` | `ruangcloud2026` | 5 GB | Dashboard pengguna |
| **Pengguna** | `sona@ruangcloud.id` | `ruangcloud2026` | 5 GB | Dashboard pengguna (akun klien) |
| **Pengguna** | `ghea@ruangcloud.id` | `ruangcloud2026` | 5 GB | Dashboard pengguna |

Akun pengguna yang mendaftar sendiri melalui halaman registrasi otomatis mendapat kuota **5 GB**.

---

## 🔧 Perintah Khusus Pemeliharaan (CLI)

Pada lingkungan Docker, awali perintah dengan `docker compose exec app`.

1. **Pemeriksaan Kesehatan Node & Pemulihan Replika**:
   ```bash
   php artisan nodes:check
   ```
   *Memeriksa ketersediaan folder node, memperbarui status kesehatan, dan menjadwalkan ulang replikasi yang kurang dari target atau sempat gagal.*
2. **Pembersihan Chunk Kedaluwarsa (Garbage Collection)**:
   ```bash
   php artisan uploads:cleanup
   ```
   *Menghapus potongan chunk dan sesi unggah terbengkalai melewati masa toleransi 24 jam.*
3. **Pembersihan Data Uji**:
   ```bash
   php artisan data:clean --force
   ```
   *Membersihkan data hasil pengujian (file, folder, dan sesi) dari basis data dan penyimpanan node.*

---

## 📁 Struktur Direktori Proyek

```text
byedriand/ruangcloud/ (Root Repositori GitHub)
├── README.md                  # Dokumentasi ini
├── Project Documentation/     # Dokumen proyek (KAK/USTEK dan lampiran)
└── ruangcloud/                # Direktori utama kode aplikasi
    ├── app/                   # Controllers, Models, Jobs, Services, Support, Console Commands
    ├── bootstrap/             # Bootstrapping framework
    ├── config/                # Berkas konfigurasi sistem
    ├── database/              # Migrasi & seeder (RuangCloudSeeder)
    ├── docker/                # Konfigurasi nginx & php.ini untuk Docker
    ├── public/                # Web root (index.php, aset terpublikasi)
    ├── resources/             # Blade views, Tailwind CSS, JavaScript (uploader/downloader)
    ├── routes/                # Definisi rute (web.php)
    ├── storage/               # Chunk sementara & folder node cluster (app/nodes/*)
    ├── tests/                 # Pengujian otomatis (Unit & Feature)
    ├── Dockerfile             # Image multi-stage (build aset + PHP-FPM)
    ├── docker-compose.yml     # 7 service: web, app, worker, db, node-jakarta/bandung/surabaya
    ├── DOCKER.md              # Panduan menjalankan dengan Docker
    ├── artisan                # CLI runner Laravel
    ├── composer.json          # Dependensi backend PHP
    ├── package.json           # Dependensi frontend & skrip build
    └── vite.config.js         # Konfigurasi bundler Vite
```

---

## 🧪 Rencana Pengujian & Kriteria Kelulusan

Sesuai **Bagian VIII USTEK**, sistem disiapkan untuk melalui serangkaian pengujian terukur:

| Jenis Pengujian | Skenario Uji | Kriteria Kelulusan | Metode Pelaksanaan |
|:---|:---|:---|:---|
| **Functional Testing** | Eksekusi seluruh fitur modul F-01 s.d. F-09. | Seluruh fitur kritikal berjalan 100% tanpa galat. | Pengujian terstruktur per modul & pengujian integrasi endpoint API. |
| **Resume Testing** | Unggah file besar, putuskan koneksi di tengah jalan, lanjutkan kembali. | Lanjut dari chunk terakhir tanpa mengulang dari awal; file hasil utuh. | Simulasi interupsi jaringan di berbagai titik persentase, verifikasi SHA-256. |
| **Load Testing** | Pengujian beban multi-unggahan bersamaan. | Tetap stabil pada minimal 100 pengguna aktif simultan. | Pengujian simulasi bertahap pada antrean dan server. |
| **Failover Testing** | Mematikan satu storage node aktif secara tiba-tiba. | File tetap dapat diunduh tanpa kendala dari node replika cadangan. | Pengujian pemadaman node simulasi di lingkungan pementasan (*staging*). |
| **Security Testing** | Pengujian terhadap celah keamanan OWASP Top 10 & kontrol akses RBAC. | Tidak ditemukan kerentanan berisiko Tinggi atau Kritis. | Pemindaian keamanan berkala, sanitasi input, validasi tipe & MIME berkas. |
| **UAT (User Acceptance)** | Uji kebergunaan oleh perwakilan pengguna akhir. | Tingkat kepuasan penerimaan minimal **85%**. | Kuesioner penerimaan UAT terstruktur berdasarkan skenario kerja riil. |

---

## ⚖️ Dasar Hukum & Standar Teknis

Pelaksanaan perancangan dan pembangunan sistem RuangCloud berpedoman pada:
- **Peraturan Presiden No. 16 Tahun 2018** beserta perubahannya tentang Pengadaan Barang/Jasa Pemerintah.
- **Undang-Undang No. 11 Tahun 2008** tentang Informasi dan Transaksi Elektronik (ITE) beserta perubahannya.
- **Undang-Undang No. 27 Tahun 2022** tentang Pelindungan Data Pribadi (UU PDP).
- **Peraturan Pemerintah No. 71 Tahun 2019** tentang Penyelenggaraan Sistem dan Transaksi Elektronik (PSTE).
- Standar Keamanan **ISO/IEC 27001** dan **OWASP Application Security Verification Standard (ASVS)**.
- Standar Penulisan Kode **PSR-12 (PHP Standard Recommendation)**.

---

## 📄 Lisensi

Hak cipta dan kepemilikan hasil pekerjaan diatur sesuai ketentuan Usulan Teknis dan Kerangka Acuan Kerja (KAK). Seluruh pustaka pihak ketiga yang digunakan berlisensi *open source* (MIT/BSD/Apache-2.0) yang diizinkan untuk penggunaan instansi dan komersial.

© 2026 **Kelompok 4 - FIKSI Universitas Kebangsaan Republik Indonesia**. Seluruh hak cipta dilindungi undang-undang.
