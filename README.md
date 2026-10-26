# UTS — AdminLTE Nilai Mahasiswa

Proyek tugas UTS pengolahan nilai mahasiswa menggunakan **PHP Native, MySQLi, MySQL/MariaDB, dan template AdminLTE 4.10.0**. Tampilan dasarnya tetap memakai template AdminLTE; halaman Users disesuaikan untuk menampilkan dan mengelola data nilai mahasiswa.

## Fitur

- Menampilkan data mahasiswa dari tabel `nilaimhsw`.
- Menampilkan foto/thumbnail di samping nama mahasiswa.
- Mencari mahasiswa berdasarkan nama dan memfilter berdasarkan predikat.
- Mengurutkan data berdasarkan nilai akhir.
- Mengatur pagination 5, 10, atau 15 data per halaman.
- Menambah, mengubah, dan menghapus data mahasiswa.
- Menampilkan tanggal data dibuat dan terakhir diperbarui.

## Teknologi

- PHP Native dan MySQLi
- MySQL/MariaDB melalui XAMPP
- AdminLTE 4.10.0

## Database

Nama database yang digunakan aplikasi: `db5a`.

Tabel utama: `nilaimhsw`, dengan kolom yang digunakan aplikasi seperti `nim`, `namamhsw`, `nilaiakhir`, `nilaihuruf`, `predikat`, `foto`, `created_date`, dan `modified_date`.

### File SQL

- **`nilaimhsw.sql`** — ekspor database dari phpMyAdmin yang digunakan sebagai dump utama proyek. Simpan file ini sebagai salinan ekspor asli.
- `database_schema.sql` — alternatif untuk membuat struktur tabel pada instalasi baru.
- `database_dummy_data.sql` — data contoh untuk menguji fitur; bukan pengganti data asli.
- `database_export.sql` — paket skema dan data contoh untuk pengujian terpisah.
- `database_migration_existing.sql` — panduan perubahan struktur untuk database lama jika memang diperlukan.

File SQL tambahan disediakan sebagai bantuan untuk instalasi/pengujian. **Tidak perlu mengimpor semuanya.** Jika ingin mempertahankan database yang sudah ada, buat backup terlebih dahulu dan periksa struktur tabel sebelum menjalankan SQL tambahan. Hindari menjalankan perintah perubahan struktur yang sama lebih dari sekali.

## Menjalankan di XAMPP

1. Letakkan folder proyek di dalam `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL dari XAMPP Control Panel.
3. Buat atau pilih database `db5a` melalui phpMyAdmin.
4. Untuk memulihkan database dari ekspor utama, impor `nilaimhsw.sql`. Jika menggunakan database yang sudah berjalan, jangan mengimpor ulang sebelum memastikan dampaknya terhadap data saat ini.
5. Periksa pengaturan koneksi pada `connect.php`. Konfigurasi bawaan untuk XAMPP lokal menggunakan host `localhost`, user `root`, password kosong, dan database `db5a`.
6. Buka halaman aplikasi melalui:

   `http://localhost/admin-lte-v4.10.0/users.php`

   Sesuaikan alamat URL jika nama folder proyek berbeda.

## Foto mahasiswa

Foto bersifat opsional. Aplikasi menggunakan kolom `foto` untuk menyimpan nama file foto dan direktori `uploads/foto-mahasiswa/` untuk file yang diunggah. Untuk repositori publik, gunakan foto contoh dan jangan mengunggah foto mahasiswa asli tanpa izin.

## Pengujian

Jalankan aplikasi di XAMPP dan periksa pencarian nama, filter predikat, pengurutan nilai akhir, pagination, tambah/edit/hapus data, serta tampilan foto. Pastikan pengujian dilakukan pada salinan database atau data contoh agar data penting tidak berubah.

## Repositori

https://github.com/gusti111/Admin_Lte_Nilai_Mahasiswa
