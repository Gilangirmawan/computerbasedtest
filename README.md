# Sistem Computer Based Test (CBT) Berbasis Website
<img width="1920" height="1080" alt="Screenshot (326)" src="https://github.com/user-attachments/assets/4ca7a6b6-361a-4497-be48-b45d2b038f3a" />

Proyek ini dibuat guna memenuhi mata kuliah PKL **Praktik Kerja Lapangan** Sebuah aplikasi Computer Based Test (CBT) berbasis web yang dibangun menggunakan framework Laravel. Sistem ini dirancang untuk mengelola dan melaksanakan ujian online di lingkungan sekolah secara efisien.

Proyek ini dibuat sebagai studi kasus untuk **SMK Muhammadiyah 1 Sirampog** dan mendukung tiga peran pengguna yang berbeda: **Admin**, **Guru**, dan **Siswa**.

---

## ✨ Fitur Utama

Sistem ini memiliki fungsionalitas yang terpisah untuk setiap peran pengguna.

### 👨‍💼 Fitur Admin
* **Dashboard Utama:** Menampilkan statistik jumlah total Admin, Guru, dan Siswa.
* **Manajemen Pengguna (Siswa):**
    * CRUD (Create, Read, Update, Delete) data siswa.
    * Menyetujui (`Approve`) atau Menolak (`Reject`) akun siswa baru yang mendaftar.
    * Filter data siswa berdasarkan **Jurusan** dan **Status Akun** (Approved, Rejected, Submitted).
    * Import data siswa secara massal dari file Excel.
* **Manajemen Pengguna (Guru):**
    * CRUD (Create, Read, Update, Delete) data guru.
    * Import data guru secara massal.
* **Manajemen Data Master:**
    * Mengelola data Kelas.
    * Mengelola data Jurusan.
    * Mengelola data Mata Pelajaran.
* **Ganti Password:** Fitur keamanan untuk mengubah password akun admin.

### 🧑‍🏫 Fitur Guru
* **Dashboard Guru:** Menampilkan ringkasan data guru.
* **Bank Soal:**
    * Membuat, membaca, memperbarui, dan menghapus soal-soal ujian.
    * Import soal dari file Excel.
* **Manajemen Ujian:**
    * Membuat dan menjadwalkan ujian baru (memilih mapel, kelas, jurusan, durasi, dll).
    * Melihat hasil ujian yang telah dikerjakan siswa.
* **Ganti Password:** Fitur keamanan untuk mengubah password akun guru.

### 🎓 Fitur Siswa
* **Dashboard Siswa:** Menampilkan profil siswa dan daftar ujian yang tersedia.
* **Pengerjaan Ujian:**
    * Mengikuti ujian yang sedang aktif dan dijadwalkan untuk kelas/jurusannya.
    * Antarmuka ujian yang aman dengan token.
* **Riwayat Ujian:** Melihat riwayat dan nilai ujian yang telah selesai dikerjakan.
* **Ganti Password:** Fitur keamanan untuk mengubah password akun siswa.

---

## 📸 Galeri Screenshot

---

## 🛠️ Tumpukan Teknologi (Tech Stack)

* **Backend:** [PHP 8.2](https://www.php.net/), [Laravel](https://laravel.com/)
* **Frontend:** HTML, [Bootstrap 5](https://getbootstrap.com/), JavaScript
* **Database:** MySQL
* **Paket Utama:**
    * [Maatwebsite/excel](https://laravel-excel.com/) untuk fungsionalitas import data.
    * [SweetAlert2](https://sweetalert2.github.io/) untuk notifikasi dan pop-up konfirmasi yang interaktif.

---

## 🚀 Instalasi dan Penyiapan

Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan lokal Anda.

### Prasyarat
* PHP >= 8.2
* Composer
* Server Lokal (Laragon, XAMPP, dll)
* Database (MySQL/MariaDB)

### Langkah-langkah Instalasi
1.  **Clone repositori ini:**
    ```bash
    git clone [https://github.com/USERNAME_ANDA/NAMA_REPO_ANDA.git](https://github.com/USERNAME_ANDA/NAMA_REPO_ANDA.git)
    cd NAMA_REPO_ANDA
    ```

2.  **Install dependensi:**
    ```bash
    composer install
    ```

3.  **Salin file environment:**
    ```bash
    cp .env.example .env
    ```

4.  **Konfigurasi file `.env`:**
    Buka file `.env` dan atur koneksi database Anda (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_sistem_cbt
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Generate key aplikasi:**
    ```bash
    php artisan key:generate
    ```

6.  **Jalankan migrasi database:**
    (Ini akan membuat semua tabel yang diperlukan di database Anda)
    ```bash
    php artisan migrate
    ```

7.  **(Opsional) Jalankan Seeder:**
    Jika Anda memiliki *database seeders* untuk data awal (seperti akun admin), jalankan:
    ```bash
    php artisan db:seed
    ```
    
8.  **Jalankan server pengembangan:**
    ```bash
    php artisan serve
    ```

Aplikasi Anda sekarang berjalan di `http://127.0.0.1:8000`.

---

## 🧑‍💻 Akun Demo

* **Admin**
    * **Username:** `admin`
    * **Password:** `admin123`
* **Guru**
    * **Username:** `sesuai nip`
    * **Password:** `sama dengan username`
