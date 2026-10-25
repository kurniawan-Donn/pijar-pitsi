# Buku Panduan PILARA

**PILARA** adalah platform magang PT Pilar Teknotama Sinergi (PITSI) untuk pendaftaran, biodata,
logbook harian, absensi dan uang saku, penilaian, feedback, surat keterangan, hingga alumni.
*Tumbuh bersama, kokoh berkarya.*

| Dokumen | Untuk |
|---|---|
| [Buku Alur Magang](alur-magang.md) | Semua: alur dari mendaftar sampai menjadi alumni, beserta diagram |
| [Panduan Peserta](panduan-peserta.md) | Mahasiswa / peserta magang |
| [Panduan Admin](panduan-admin.md) | Admin |
| [Panduan Super Admin](panduan-super-admin.md) | Super Admin (pengaturan sistem) |

Panduan yang sama bisa dibaca di dalam aplikasi lewat menu **Panduan** di sidebar; isinya
menyesuaikan role yang sedang login.

## Catatan untuk Pengembang

- File di folder ini adalah **sumber tunggal**: halaman Panduan di web membaca file Markdown ini
  (`App\Services\BukuPanduan`). Ubah teks di sini, web ikut berubah.
- Gambar ada di `public/images/panduan/` dan dirujuk dengan path relatif
  `../../public/images/panduan/…` agar tampil di GitHub maupun di web.
- Screenshot memakai **data contoh fiktif** dari `Database\Seeders\PanduanDemoSeeder` pada database
  terpisah, bukan data asli:

  ```
  DB_DATABASE=database/panduan.sqlite php artisan migrate:fresh --seeder=PanduanDemoSeeder
  ```

  Jangan jalankan perintah itu tanpa `DB_DATABASE` terpisah karena `migrate:fresh` mengosongkan
  database.
- Screenshot diambil ulang otomatis dengan [`alat/ambil_screenshot.py`](alat/ambil_screenshot.py)
  (Chrome headless). Langkah lengkapnya ada di bagian atas skrip. Setelah selesai, hapus database
  demo dan foto demo yang dibuat seeder di `storage/`.
