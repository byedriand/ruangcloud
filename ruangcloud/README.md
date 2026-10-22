# RuangCloud

> **Perancangan dan Pembangunan Sistem Informasi Penyimpanan Cloud Terdistribusi Berbasis Web Menggunakan Metode Resumable Chunk Upload**  
> *Usulan Teknis (USTEK) - Kelompok 4 | Fakultas Ilmu Komputer dan Sistem Informasi (FIKSI), Universitas Kebangsaan Republik Indonesia (UKRI), Bandung (2026)*

---

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
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
   - [Alur Resumable Chunk Upload](#1-alur-resumable-chunk-upload)
   - [Penyimpanan Terdistribusi & Replikasi](#2-penyimpanan-terdistribusi--replikasi)
   - [Health Check & Failover Otomatis](#3-health-check--failover-otomatis)
8. [Tumpukan Teknologi (Tech Stack)](#-tumpukan-teknologi-tech-stack)
9. [Panduan Instalasi & Menjalankan](#-panduan-instalasi--menjalankan)
10. [Akun Demo Pengujian](#-akun-demo-pengujian)
11. [Perintah Khusus Pemeliharaan (CLI)](#-perintah-khusus-pemeliharaan-cli)
12. [Struktur Direktori Proyek](#-struktur-direktori-proyek)
13. [Rencana Pengujian & Kriteria Kelulusan](#-rencana-pengujian--kriteria-kelulusan)
14. [Dasar Hukum & Standar Teknis](#-dasar-hukum--standar-teknis)

---

## 📖 Tentang Proyek

**RuangCloud** adalah sistem informasi penyimpanan awan (cloud storage) mandiri yang dirancang untuk mengatasi keterbatasan unggah file berukuran besar dan mengeliminasi *Single Point of Failure* (SPOF) pada peladen tunggal. 

Sistem ini mengimplementasikan metode **Resumable Chunk Upload** (pemotongan berkas di sisi peramban klien dengan verifikasi checksum per chunk) serta **Penyimpanan Terdistribusi Multi-Node** dengan replikasi asinkron otomatis dan mekanisme failover cerdas.

---

## 👥 Tim Pengembang

Disusun dan dikembangkan oleh **Kelompok 4**  
**Program Studi Informatika / Sistem Informasi**  
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

1. **Kegagalan Unggah File Berukuran Besar**: Unggah berkas berkapasitas besar (≥ 5 GB) seringkali mengalami kegagalan akibat timeout koneksi HTTP atau fluktuasi jaringan, mengharuskan pengguna mengulang proses dari 0%.
2. **Single Point of Failure (SPOF)**: Penyimpanan yang hanya bertumpu pada satu peladen fisik berisiko tinggi terhadap kehilangan data permanen dan terhentinya operasional layanan saat peladen mengalami kerusakan (*down*).
3. **Ketiadaan Tata Kelola Terpusat**: Belum adanya manajemen hak akses berbasis peran (RBAC), batas kuota penyimpanan, pembagian tautan terkontrol, dan jejak audit (*audit trail*) menyeluruh yang dapat dipertanggungjawabkan.

---

## 📊 Ruang Lingkup & Sasaran Keberhasilan

| No | Sasaran KAK / USTEK | Indikator Keberhasilan | Pendekatan Solusi RuangCloud |
|:--:|:---|:---|:---|
| 1 | **Unggah File Besar Andal** | File minimal 5 GB (hingga 20 GB) berhasil diunggah & dapat dilanjutkan dari chunk terakhir. | Client-side chunking (default 5 MB), session status di server, auto retry dengan exponential backoff, resume hingga 24 jam. |
| 2 | **Ketersediaan Data Tinggi (HA)** | Minimal 2 replika pada node berbeda; layanan tetap beroperasi saat 1 node padam. | Replikasi asinkron via Laravel Queue, penempatan berbasis kapasitas, health check otomatis, dan read failover. |
| 3 | **Integritas Data Mutlak** | Verifikasi Checksum SHA-256 berhasil 100% pada berkas hasil penggabungan. | Validasi hash SHA-256 per chunk, checksum total akhir, dan verifikasi ulang salinan replika. |
| 4 | **Kinerja Memadai** | Menangani beban minimal 100 pengguna aktif bersamaan. | Antrean asinkron (queue worker), Redis/database cache, penggabungan berkas berbasis stream non-blocking. |
| 5 | **Tingkat Kebergunaan** | Tingkat penerimaan pengujian pengguna (UAT) minimal 85%. | Antarmuka responsif ramah pengguna (desktop & mobile), progress bar interaktif, dialog konfirmasi. |

---

## 🏗 Arsitektur Sistem (4 Lapisan)

Sistem RuangCloud dirancang dengan arsitektur 4 lapisan modular (*four-tier decoupled architecture*):

```
┌────────────────────────────────────────────────────────────────────────┐
│                        LAPISAN KLIEN (FRONTEND)                        │
│    Modern Web Browsers (Chrome, Firefox, Safari, Edge)                │
│    • HTML5 File API (Blob Slicing & SHA-256 Crypto API)               │
│    • Responsive UI (Tailwind CSS v4 + Vanilla JS Component System)     │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ HTTP / RESTful API (Chunk & Auth)
┌───────────────────────────────────▼────────────────────────────────────┐
│                       LAPISAN APLIKASI (BACKEND)                       │
│    Laravel 12 REST API Framework                                       │
│    • Session & Chunk Ingestion Coordinator                             │
│    • Role-Based Access Control (RBAC) & Quota Enforcement              │
│    • Background Queue Workers (AssembleUpload & ReplicateFile)         │
└──────────────────┬─────────────────────────────────┬───────────────────┘
                   │                                 │
                   │ Query & Transaction             │ Read / Write / Health
┌──────────────────▼───────────────┐ ┌───────────────▼───────────────────┐
│       LAPISAN DATA (DATABASE)     │ │  LAPISAN PENYIMPANAN TERDISTRIBUSI │
│  MySQL Database:                 │ │  Cluster Storage Nodes (Min. 2 Node)│
│  • Metadata File & Folder Bersarang│ │  • Node Jakarta (Primary / Replica)│
│  • Status Sesi Chunk & Upload    │ │  • Node Bandung (Primary / Replica) │
│  • RBAC, Kuota, Share Links      │ │  • Node Surabaya (Primary / Replica)│
│  • Audit Trail & Activity Logs   │ │  (Mendukung Local Disk & MinIO/S3) │
└──────────────────────────────────┘ └───────────────────────────────────┘
```

---

## 🚀 Fitur Utama & Pemenuhan Kebutuhan Fungsional

Sistem mengimplementasikan seluruh kebutuhan fungsional modul **F-01 hingga F-09**:

- **F-01: Autentikasi dan Otorisasi (RBAC)**  
  Mekanisme autentikasi aman dengan *Role-Based Access Control* memisahkan hak akses antara **Administrator** dan **Pengguna**. Password diamankan dengan hashing bcrypt, dilengkapi proteksi serangan CSRF dan session fixation.
- **F-02: Manajemen File dan Folder Lengkap**  
  Pengorganisasian berkas dengan struktur folder bertingkat (*nested folders*), breadcrumb navigasi, pembuatan folder, pengubahan nama (*rename*), pemindahan berkas, pencarian cepat (*search filter*), pengunduhan langsung, dan sistem tempat sampah (*Trash / Soft Delete*) sebelum penghapusan permanen.
- **F-03: Resumable Chunk Upload (Anti-Gagal)**  
  Berkas besar dipecah di sisi klien menjadi potongan-potongan (default: 5 MB) dan diunggah secara sekuensial. Mendukung jeda (*pause*), lanjutkan (*resume*), dan pembatalan (*cancel*). Jika koneksi terputus, sesi bertahan selama 24 jam dan unggah dapat dilanjutkan langsung dari chunk terakhir tanpa mengulang.
- **F-04: Progress dan Status Unggah Real-Time**  
  Panel modal unggah interaktif yang menyajikan progress bar akurat, indikator persentase, kalkulasi kecepatan transfer rata-rata, estimasi sisa waktu unggah, dan antrean berkas (*upload queue*).
- **F-05: Penyimpanan Terdistribusi & Replikasi Asinkron**  
  File disimpan pada node primer yang dipilih secara cerdas berdasarkan sisa kapasitas penyimpanan terbanyak (*capacity-aware placement*). File kemudian direplikasi secara asinkron ke node lain (minimal 2 replika) melalui antrean background worker.
- **F-06: Berbagi File Terkontrol (Secure Share Link)**  
  Fitur tautan berbagi publik (`/s/{token}`) yang dapat dikonfigurasi dengan kata sandi pengaman opsional, batas tanggal kedaluwarsa (*expiration date*), dan kemampuan pencabutan hak akses sewaktu-waktu oleh pemilik file.
- **F-07: Manajemen Kuota Penyimpanan Pengguna**  
  Alokasi kuota per pengguna dengan widget indikator kapasitas pada sidebar. Dilengkapi peringatan visual dini saat kuota terpakai mencapai ambang batas kritis (≥ 85%).
- **F-08: Dashboard Administrator Cerdas**  
  Pusat kendali admin untuk memantau kapasitas total cluster, visualisasi status operasional masing-masing storage node (*Active / Offline*), statistik aktivitas unggahan berkas harian, dan ringkasan audit log sistem.
- **F-09: Log Aktivitas & Jejak Audit (Audit Trail)**  
  Pencatatan komprehensif atas seluruh aksi penting sistem (Login, Unggah, Hapus, Pulihkan, Bagikan, Ganti Nama, Failover Node) yang dilengkapi timestamp, identitas aktor, dan alamat IP klien untuk akuntabilitas.

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
      ├── 3. PUT /upload/{id}/chunk (Chunk #1) ────>│ Verifikasi Ukuran & SHA-256      │
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
- **Penempatan Node Primer**: Saat proses penggabungan berkas selesai, sistem memeriksa node penyimpanan yang berstatus `active` dan memilih node dengan ruang penyimpanan bebas terbesar.
- **Faktor Replikasi**: File otomatis disalin ke node cadangan hingga memenuhi target replikasi (default: 2 salinan fisik di node berbeda).
- **Verifikasi Integritas Ganda**: Checksum SHA-256 dihitung pada file hasil gabungan di node primer dan diverifikasi ulang terhadap salinan di node replika.

### 3. Health Check & Failover Otomatis
- Jika node primer mengalami gangguan fisik atau padam (*offline*), proses pengunduhan berkas dialihkan secara transparan (*transparent read failover*) ke node replika yang aktif tanpa menyebabkan *downtime* bagi pengguna.
- Perintah artisan berkala mendeteksi ketidaksesuaian jumlah replika dan menjadwalkan ulang replikasi otomatis (*self-healing replication*).

---

## 🛠 Tumpukan Teknologi (Tech Stack)

| Komponen | Teknologi | Keterangan |
|:---|:---|:---|
| **Backend Framework** | Laravel 12.x (PHP 8.2+) | RESTful API, Blade Component Engine, Queue Jobs, Eloquent ORM |
| **Frontend UI** | Vanilla JS + Tailwind CSS v4 | Responsif, tanpa dependensi framework berat, performa tinggi |
| **Database** | MySQL 8.0+ | Transaksional, relasi foreign key berindeks, UTF8MB4 |
| **Queue / Asinkron** | Laravel Database Queue / Redis | Background processing penggabungan file & sinkronisasi replika |
| **Penyimpanan Node** | Local Storage Multi-Directory / S3 | Simulasi multi-node (Jakarta, Bandung, Surabaya) / Object Storage |
| **Keamanan & Standar** | SHA-256, Bcrypt, PSR-12, OWASP | Validasi MIME type, integritas cryptographic hash, CSRF protection |

---

## 💻 Panduan Instalasi & Menjalankan

### Prasyarat Sistem
- **PHP** versi 8.2 atau lebih baru (ekstensi aktif: `fileinfo`, `pdo_mysql`, `mbstring`, `openssl`).
- **Composer** versi 2.x.
- **Node.js** (v18+) & **npm**.
- **MySQL / MariaDB** (dapat menggunakan bundel XAMPP).
- Konfigurasi PHP (`php.ini`): pastikan `upload_max_filesize` dan `post_max_size` minimal `20M` (default XAMPP 40M sudah sangat memadai karena pengiriman menggunakan chunk 5MB).

---

### Langkah Instalasi

#### 1. Clone Repositori & Masuk ke Folder Proyek
```bash
git clone https://github.com/byedriand/ruangcloud.git
cd ruangcloud/ruangcloud
```

#### 2. Pasang Dependensi PHP & JavaScript
```bash
composer install
npm install
```

#### 3. Konfigurasi Environment (`.env`)
Salin file template `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi koneksi database MySQL Anda di `.env`:
```env
APP_NAME=RuangCloud
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ruangcloud
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

Generate application key:
```bash
php artisan key:generate
```

#### 4. Migrasi Database & Seeding Data Awal
Nyalakan Apache & MySQL pada panel kontrol XAMPP, buat database bernama `ruangcloud`, lalu jalankan:
```bash
php artisan migrate
php artisan db:seed --class=RuangCloudSeeder
```
*Perintah ini akan membuat struktur tabel lengkap dan menginisialisasi akun demo serta 3 node penyimpanan (Jakarta, Bandung, Surabaya).*

---

### Menjalankan Aplikasi

Jalankan **3 terminal** terpisah pada direktori proyek:

- **Terminal 1 (Web Server)**:
  ```bash
  php artisan serve
  ```
- **Terminal 2 (Queue Worker - Wajib untuk Penggabungan & Replikasi)**:
  ```bash
  php artisan queue:work --timeout=3600
  ```
  *(Catatan: Worker ini wajib berjalan agar proses penggabungan chunk file dan replikasi antar node dapat diselesaikan).*
- **Terminal 3 (Vite Asset Bundler)**:
  ```bash
  npm run dev
  ```

Buka peramban di: **`http://127.0.0.1:8000`** *(Gunakan `127.0.0.1` atau `localhost` demi menjamin tersedianya secure Web Crypto API untuk hashing SHA-256 di browser)*.

---

## 🔑 Akun Demo Pengujian

Seluruh akun demo memiliki kata sandi default: **`ruangcloud2026`**

| Role | Email | Password | Kapasitas Kuota | Akses Halaman |
|:---|:---|:---:|:---:|:---|
| **Administrator** | `admin@ruangcloud.id` | `ruangcloud2026` | 1.000 GB | `/admin` (Cluster Dashboard, Nodes Health, Audit Log) |
| **Pengguna** | `naufal@ruangcloud.id` | `ruangcloud2026` | 100 GB | `/dashboard` (Manajemen Berkas, Upload, Berbagi) |
| **Pengguna** | `sona@ruangcloud.id` | `ruangcloud2026` | 100 GB | `/dashboard` (Akun Klien) |
| **Pengguna** | `ghea@ruangcloud.id` | `ruangcloud2026` | 100 GB | `/dashboard` |

---

## 🔧 Perintah Khusus Pemeliharaan (CLI)

RuangCloud menyediakan perintah Artisan khusus untuk administrasi latar belakang:

1. **Pemeriksaan Kesehatan Node & Pemulihan Replika (Self-Healing)**:
   ```bash
   php artisan nodes:check
   ```
   *Memeriksa ketersediaan folder node, memperbarui status kesehatan, melengkapi file yang replikanya kurang dari target, dan mengulangi replikasi yang sempat gagal.*

2. **Pembersihan Chunk Kedaluwarsa (Garbage Collection)**:
   ```bash
   php artisan uploads:cleanup
   ```
   *Menghapus potongan chunk dan sesi upload yang telah terbengkalai melewati masa toleransi 24 jam untuk mencegah pemborosan ruang penyimpanan.*

*(Dapat dijadwalkan otomatis melalui scheduler Laravel `php artisan schedule:work`)*.

---

## 📁 Struktur Direktori Proyek

Seluruh source code aplikasi dikelompokkan rapi di dalam direktori `ruangcloud/`:

```text
byedriand/ruangcloud/ (Root Repository)
├── ruangcloud/           # Direktori utama kode aplikasi sistem RuangCloud
│   ├── app/              # Logika inti aplikasi (Controllers, Models, Jobs, Services, CLI Commands)
│   │   ├── Console/      # Perintah artisan kustom (nodes:check, uploads:cleanup, dev)
│   │   ├── Http/         # Kontroler web & REST API (Upload, Auth, File, Share, Admin, dll.)
│   │   ├── Jobs/         # Background queue jobs (AssembleUpload, ReplicateFile)
│   │   ├── Models/       # Eloquent ORM Models (User, StoredFile, StorageNode, ShareLink, dll.)
│   │   └── Services/     # Layanan penyimpanan & failover (StorageNodeService)
│   ├── bootstrap/        # Bootstrapping framework & registrasi provider
│   ├── config/           # Berkas konfigurasi sistem (config/nodes.php, queue.php, dll.)
│   ├── database/         # Migrasi skema database, factories, & seeder (RuangCloudSeeder)
│   ├── docs/             # Dokumentasi riwayat & catatan teknis tahap pengembangan
│   │   ├── README-TAHAP2.md  # Catatan teknis backend, database, chunk logic, & multi-node
│   │   └── README-TAHAP3.md  # Catatan teknis integrasi halaman user, share link, & soft delete
│   ├── public/           # Web server public root (index.php, robots.txt, favicon)
│   ├── resources/        # Aset kode sumber frontend
│   │   ├── css/          # Stylesheet Tailwind CSS v4 & custom styles
│   │   ├── js/           # JavaScript klien (uploader.js, ruangcloud.js)
│   │   └── views/        # Template antarmuka Laravel Blade (Admin, User, Share, Auth)
│   ├── routes/           # Definisi rute aplikasi (web.php, console.php)
│   ├── storage/          # Penyimpanan internal (chunks upload sementara & folder node cluster)
│   ├── tests/            # Otomasi pengujian sistem (Unit & Feature tests)
│   ├── .env.example      # Template konfigurasi environment lokal
│   ├── artisan           # CLI runner Laravel
│   ├── composer.json     # Definisi dependensi backend PHP
│   ├── package.json      # Definisi dependensi frontend JavaScript & build tools
│   └── vite.config.js    # Konfigurasi bundler Vite
├── .gitignore            # Filter pengabaian git untuk root & subfolder
└── README.md             # Dokumentasi lengkap proyek sesuai USTEK
```

---

## 🧪 Rencana Pengujian & Kriteria Kelulusan

Sesuai **Bagian VIII USTEK**, sistem telah disiapkan untuk melalui serangkaian pengujian terukur:

| Jenis Pengujian | Skenario Uji | Kriteria Kelulusan | Metode Pelaksanaan |
|:---|:---|:---|:---|
| **Functional Testing** | Eksekusi seluruh fitur modul F-01 s.d. F-09. | Seluruh fitur kritikal berjalan 100% tanpa galat. | Pengujian terstruktur per modul & pengujian integrasi endpoint API. |
| **Resume Testing** | Unggah file besar, putuskan koneksi di tengah jalan, lanjutkan kembali. | Lanjut dari chunk terakhir tanpa mengulang dari awal; file hasil utuh. | Simulasi interupsi jaringan di berbagai titik persentase, verifikasi SHA-256. |
| **Load Testing** | Pengujian beban multi-unggahan bersamaan. | Tetap stabil pada minimal 100 pengguna aktif simultan. | Pengujian simulasi bertahap pada antrean dan server. |
| **Failover Testing** | Mematikan satu storage node aktif secara tiba-tiba. | File tetap dapat diunduh tanpa kendala dari node replika cadangan. | Pengujian pemadaman node simulasi di lingkungan pementasan (*staging*). |
| **Security Testing** | Pengujian terhadap celah keamanan OWASP Top 10 & kontrol akses RBAC. | Tidak ditemukan kerentanan berisiko Tinggi atau Kritis (*Zero High/Critical Vulnerabilities*). | Pemindaian keamanan berkala, sanitasi input, validasi tipe & MIME berkas. |
| **UAT (User Acceptance)**| Uji kebergunaan oleh perwakilan pengguna akhir. | Tingkat kepuasan penerimaan minimal **85%**. | Kuesioner penerimaan UAT terstruktur berdasarkan skenario kerja riil. |

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
