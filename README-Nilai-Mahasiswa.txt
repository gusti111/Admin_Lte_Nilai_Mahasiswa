INTEGRASI TEMPLATE ADMINLTE 4.10.0 — NILAI MAHASISWA

Perubahan dibuat langsung dari halaman users.html bawaan template. Halaman tersebut diganti namanya menjadi users.php (bukan membuat dashboard lain), karena server PHP/XAMPP secara standar memproses kode PHP pada file berekstensi .php.

Yang dipertahankan:
- Struktur halaman, sidebar, navbar, card, modal, Bootstrap/AdminLTE, CSS, JavaScript, gambar, dan aset asli template.
- Direktori aset asli AdminLTE tetap lengkap.

Integrasi database:
- connect.php: memakai konfigurasi dari proyek phph_db, database db5a, user root, password kosong.
- Tabel: nilaimhsw.
- Kolom utama: nim, namamhsw, nilaiakhir, nilaihuruf, predikat. Kolom created_date dan modified_date ditampilkan/diperbarui jika tersedia pada tabel.
- nilai_predikat_huruf.php: menggunakan fungsi predikat() dengan label dan kategori dari tugas yang kamu kirim.
- insert_nilai.php: POST tambah data, validasi NIM/nama/nilai, hitung nilai huruf/predikat, prepared statement.
- edit_nilai.php: POST edit data; form modal mengambil value NIM, nama, dan nilai dari tombol Edit.
- delete_nilai.php: POST hapus data; tombol memiliki atribut data-nim yang disalin ke input tersembunyi pada modal konfirmasi.
- _nilai_helpers.php: validasi dan fungsi redirect yang dipakai handler.

Cara menjalankan:
1. Ekstrak ZIP ini ke C:\xampp\htdocs\.
2. Aktifkan Apache dan MySQL dari XAMPP.
3. Pastikan database db5a dan tabel nilaimhsw dari proyek phph_db tersedia di phpMyAdmin.
4. Buka http://localhost/admin-lte-v4.10.0/users.php
5. Jika nama database, user, atau password berbeda, sesuaikan connect.php.

Catatan:
- `users.html` asli diganti menjadi `users.php`; halaman AdminLTE yang sama dipakai, bukan page baru yang didesain dari awal.
- Data email dari contoh template tidak ditampilkan karena tabel nilaimhsw di proyekmu tidak memiliki kolom email. Kolom terkait diubah menjadi Nilai Akhir, Nilai Huruf, Predikat, dan Tgl Dibuat.
- `created_date` diasumsikan tersedia karena dibaca oleh bacanilai.php milik proyekmu. Kolom `modified_date` dipakai pada update sesuai file baca yang kamu kirim.
- Sintaks PHP dapat diperiksa lokal, tetapi koneksi MySQL hanya dapat dipastikan setelah dijalankan pada XAMPP dengan database db5a milikmu.
