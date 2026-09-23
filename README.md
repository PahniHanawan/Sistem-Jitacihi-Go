# 🍓 Jitanichi Go - Sistem Pendukung Keputusan (SPK)

**Jitanichi Go** adalah aplikasi Sistem Pendukung Keputusan berbasis web yang dirancang khusus untuk toko Jitanichi Go guna merekomendasikan produk mana yang layak mendapatkan promosi (seperti Diskon, Buy 1 Get 1, dll) berdasarkan performa penjualan masa lalu.

Aplikasi ini dibangun menggunakan algoritma **AHP (Analytical Hierarchy Process)** untuk pembobotan kriteria dan **SAW (Simple Additive Weighting)** untuk proses pemeringkatan produk.

---

## ✨ Fitur Utama

1. **Autentikasi Multi-Role**
   - **Owner**: Memiliki akses penuh termasuk menetapkan bobot AHP, melihat hasil pemeringkatan, memberikan keputusan promosi akhir, dan mencetak laporan resmi.
   - **Admin**: Bertugas mengelola data master (Produk, Kriteria, User) dan menginput data penjualan (Product Assessment) harian/bulanan untuk dievaluasi.

2. **Manajemen Data Master**
   - Kelola Data Produk (Kode, Nama, Harga Pokok, Harga Jual, Status).
   - Kelola Data Kriteria Penilaian (Benefit / Cost).
   - Kelola Periode Penilaian (Sistem proteksi: hanya bisa ada satu periode *aktif* pada satu waktu).

3. **Smart Data Entry (Penilaian Produk)**
   - Fitur cerdas yang otomatis menarik data *Sisa Stok*, *Harga Pokok*, dan *Harga Jual* dari periode sebelumnya (berdasarkan histori rekam jejak) untuk meminimalisir kesalahan input admin (*Human Error*).

4. **Kalkulasi Cerdas (AHP & SAW)**
   - Pembobotan dinamis menggunakan matriks perbandingan berpasangan (AHP) lengkap dengan fitur *Auto-fill* nilai referensi pakar.
   - Pemeringkatan (SAW) transparan menampilkan matriks keputusan, matriks ternormalisasi, hingga hasil akhir (Nilai Preferensi $V_i$).

5. **Laporan & Cetak PDF**
   - Rekapitulasi komprehensif yang menampilkan Bobot AHP, Ranking SAW, dan Keputusan Final Promosi Owner dalam satu halaman elegan yang siap cetak.
   
6. **UI/UX Modern & Interaktif**
   - Dibangun dengan **Tailwind CSS** untuk tampilan *dashboard* yang memukau dan modern.
   - Dilengkapi dengan **SweetAlert2** untuk setiap konfirmasi (hapus data, notifikasi sukses, dan *logout*).

---

## 🛠️ Tech Stack

- **Framework:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL
- **Frontend Styling:** Tailwind CSS
- **Interactivity:** Alpine.js & SweetAlert2
- **Icons:** Heroicons (SVG)

---

## 🚀 Instalasi & Konfigurasi

Berikut langkah-langkah untuk menjalankan *project* ini secara lokal di komputer Anda:

1. **Clone Repository**
   ```bash
   git clone https://github.com/PahniHanawan/Sistem-Jitacihi-Go.git
   cd Sistem-Jitacihi-Go
   ```

2. **Install Dependensi PHP & Node**
   Pastikan Anda sudah menginstal Composer dan Node.js.
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Konfigurasi Environment**
   Duplikat file `.env.example` menjadi `.env`, lalu sesuaikan koneksi database MySQL Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi & Seeding Database**
   Jalankan perintah ini untuk membangun struktur tabel dan mengisi data awal (termasuk akun Owner & Admin, Kriteria default, Produk, dan Histori Penilaian dummy untuk tes).
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Server**
   ```bash
   php artisan serve
   ```
   Aplikasi sekarang dapat diakses melalui browser pada `http://127.0.0.1:8000`.

---

## 🔐 Akses Login Default

Setelah menjalankan langkah *seeding* di atas, Anda dapat masuk menggunakan akun percobaan berikut:

| Peran | Email | Password |
| --- | --- | --- |
| **Owner** | owner@jitanichigo.com | password123 |
| **Admin** | admin@jitanichigo.com | password123 |

---

## 📄 Lisensi
Sistem Pendukung Keputusan ini dirancang khusus untuk menunjang operasional evaluasi promosi Toko Jitanichi Go.
