# Manajemen Dokumen Proyek

Website untuk mengelola dan menyimpan dokumen proyek secara terpusat.

## Fitur

- Manajemen dokumen proyek
- Sistem autentikasi pengguna
- Upload dan download file
- Antarmuka web yang responsif

## Persyaratan

- Laragon (atau PHP + MySQL)
- Web browser modern

## Instalasi

### 1. Setup Database

1. Jalankan Laragon
2. Klik `Start All`
3. Buka phpMyAdmin:
   - Dari Laragon, atau
   - Akses: `http://localhost/phpmyadmin`

4. Login phpMyAdmin dengan:
   - Username: `root`
   - Password: (kosong)

5. Import database:
   - Klik tab `Import`
   - Pilih file `database.sql` dari folder proyek
   - Klik `Go`

### 2. Menjalankan Website

Buka browser dan akses:
```
http://localhost/manajemen-dokumen-proyek/
```

> **Catatan:** Jangan membuka file dengan path `file:///...` karena database tidak akan aktif. Website harus diakses melalui `http://localhost/...`

## Kredensial Default

Akun login default untuk testing:

- **Email:** `taufiq.komara@persija.id`
- **Password:** `admin123`

## Struktur Proyek

```
├── index.html           # Halaman utama
├── api/
│   ├── config.php       # Konfigurasi database
│   ├── login.php        # API login
│   └── projects.php     # API manajemen proyek
├── uploads/             # Folder penyimpanan file upload
├── database.sql         # Script database
└── README.md           # Dokumentasi ini
```

## Konfigurasi Database

Pengaturan koneksi database dapat dilihat di `api/config.php`.

**Catatan Keamanan:** Password pengguna disimpan sebagai hash, bukan teks biasa. Untuk menambah user baru, gunakan fungsi PHP `password_hash()`.

## Teknologi

- PHP
- MySQL
- HTML/CSS/JavaScript

## Catatan Penting

- File yang diupload disimpan di folder `uploads/`
- Sesuaikan URL jika nama folder proyek berbeda
- Pastikan MySQL dan PHP berjalan dengan baik sebelum mengakses website
