## Tentang Aplikasi Buku Tamu Digital

Buku Tamu Digital merupakan sebuah aplikasi web untuk manajemen kunjungan tamu yang memudahkan proses pencatatan, persetujuan, dan dokumentasi kunjungan di perusahaan.

## Entity Relationship Diagram (ERD) 
![KP Desnet-ERD](https://github.com/user-attachments/assets/4b380dff-768a-4d7f-abfa-814d30d43c33)

## Fitur Utama

### Untuk Tamu
- **Form Kunjungan**: Isi data diri, tujuan kunjungan, dan foto KTP
- **Pilih Karyawan**: Tentukan karyawan yang ingin ditemui
- **Notifikasi Email**: Terima pemberitahuan status kunjungan
- **Akses Notulensi**: Lihat dan unduh notulensi dan dokumentasi rapat yang telah dilaksanakan melalui email

### Untuk Resepsionis
- **Dashboard Real-time**: Pantau kunjungan hari ini
- **Manajemen Kunjungan**: Buat, terima, atau tolak kunjungan tamu
- **Riwayat Kunjungan**: Lihat semua data kunjungan dengan   fitur filter dan export
- **Manajemen Karyawan**: Kelola data karyawan dan undang resepsionis baru lewat email otomatis untuk membuat akun di aplikasi
- **Lihat KTP**: Akses foto KTP tamu 

### Untuk Karyawan
- **Konfirmasi Kunjungan**: Terima atau tolak kunjungan via email
- **Form Notulensi**: Isi notulensi dan dokumentasi rapat lalu lihat ulang kapan saja
- **Upload Dokumentasi**: Tambahkan foto dokumentasi kunjungan/rapat

## Workflow Aplikasi

1. **Tamu** mengisi form kunjungan dan mengambil foto KTP
2. **Sistem** mengirim email notifikasi ke karyawan yang dituju
3. **Karyawan** menerima/menolak kunjungan via link email
4. **Resepsionis** melihat status kunjungan di dashboard
5. Jika diterima, **Karyawan** mengisi notulensi setelah rapat selesai
6. **Sistem** mengirim email hasil notulensi dan dokumentasi ke tamu
7. **Tamu** dan **Karyawan** dapat mengakses dan mengunduh notulensi kunjungan/rapat yang telah diikuti kapan saja
8. **Resepsionis** dapat melihat riwayat kunjungan secara keseluruhan

## Teknologi yang Digunakan
- **PHP Framework**: Laravel 12
- **Database**: Supabase
- **Frontend**: Blade, TailwindCSS
- **Icons**: Blade Icons
- **Cloud Storage**: Cloudinary
- **Email**: SMTP Gmail
- **Export**: jsPDF, SheetJS (Library via CDN)

## Catatan

Aplikasi ini dikembangkan untuk keperluan kerja praktik

2026
