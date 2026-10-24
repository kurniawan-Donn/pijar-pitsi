# Roadmap Pengembangan MAGENTA

Dokumen ini merangkum kondisi project saat ini, utang teknis yang ditemukan,
dan rencana pengembangan per fase (lihat juga `docs/PROMPT-PENGEMBANGAN.md`).

## 1. Fitur yang Sudah Ada

### Website publik
- Halaman utama one-page (`/`, `HomeController@index`): banner, profil perusahaan,
  dan konten dari tabel `pengaturan_websites` (informasi terbaru, pengumuman,
  persyaratan, alur magang).
- Pendaftaran diarahkan ke Google Form (`pengaturan_websites.google_form_url`).

### Autentikasi
- Login/logout (`AuthController`), redirect sesuai role:
  `super_admin` → `superadmin.dashboard`, `admin` → `admin.dashboard`.
- Pembatasan akses lewat `RoleMiddleware` (`role:admin`, `role:super_admin`,
  `role:admin,super_admin`).
- Akun awal dari `database/seeders/UserSeeder.php`.

### Area Admin (`role:admin`)
- Dashboard statis (`Route::view`, tanpa data).
- Pengajuan magang (`Admin/PengajuanMagangController`): index, create (redirect
  ke `/#pengajuan`), store, show, updateStatus, destroy. **View index & show
  masih kosong.**

### Fitur bersama Admin + Super Admin (`role:admin,super_admin`, prefix `/superadmin`)
- Profil perusahaan (satu baris di tabel `perusahaans`).
- Kelola banner (resource; hanya satu banner utama yang dipertahankan, upload
  gambar ke disk `public`).
- Kelola konten website (`PengaturanWebsiteController`).
- Data peserta magang (resource, validasi `PesertaMagangRequest`, pagination).
- Absensi peserta dibaca dari Google Sheets (tab bulan berjalan, range B11:K100)
  dan jam masuk/keluar dapat diubah langsung ke sheet
  (`GoogleSheetsService`, credential `storage/app/google/service-account.json`).

### Area khusus Super Admin (`role:super_admin`)
- Dashboard statis (`Route::view`).
- Kelola pengguna (CRUD; tidak bisa menghapus akun sendiri).

### Data
| Tabel | Model | Catatan |
|---|---|---|
| users | User | kolom `role` enum admin/super_admin |
| mahasiswas | Mahasiswa | model masih kosong |
| perusahaans | Perusahaan | dipakai untuk profil perusahaan **dan** perusahaan tujuan pengajuan |
| pengajuan_magangs | PengajuanMagang | status menunggu/disetujui/ditolak |
| banners | Banner | scope `aktif()` |
| pengaturan_websites | PengaturanWebsite | konten homepage + `google_form_url` |
| peserta_magangs | PesertaMagang | `google_sheet_url`, status aktif/selesai |
| absensis | Absensi | data absensi lokal lama (sumber utama kini Google Sheets) |

## 2. Utang Teknis

### Database / migration
- ~~Kolom `persyaratan_judul` dan `alur_judul` tidak pernah dibuat~~ —
  **diperbaiki** di migration `2026_10_24_084105_add_section_titles_to_pengaturan_websites_table`
  (dengan test `PengaturanWebsiteTest`). `pendaftaran_judul` dan
  `tentang_section_judul` hanya ada di `$fillable` model dan tidak dipakai view;
  bisa dibersihkan di Fase 1/2.
- ~~`->after()` ke kolom yang belum ada (gagal di MySQL)~~ — **diperbaiki**
  dengan menghapus `after()` dari migration `google_form_url` dan
  `google_sheet_url`.
- `peserta_magangs` dibuat kosong lalu dilengkapi di migration terpisah dengan
  semua kolom `nullable` (termasuk `nama`), dan `google_sheet_url` ditambahkan
  dua kali (dengan guard).
- Tabel `perusahaans` mencampur profil perusahaan penyelenggara dengan
  perusahaan tujuan pengajuan; kolom duplikat `nama`/`nama_perusahaan` dan
  `telepon`/`no_telepon`. `PerusahaanController` memakai `Perusahaan::first()`,
  sehingga perusahaan baru dari pengajuan bisa tertukar dengan profil.
- `mahasiswas.email` unik, sementara `store` pengajuan mencari mahasiswa
  berdasarkan NIM lalu menimpa email → bisa error unique constraint bila email
  sudah dipakai mahasiswa lain.
- Tabel `absensis` tidak lagi dipakai secara aktif (absensi di Google Sheets).

### Kode
- View dashboard berukuran 700–1.600 baris per file; CSS, sidebar, header, dan
  logout ditulis ulang di setiap view (Fase 1).
- `admin/pengajuan/index` dan `show` kosong; model `Mahasiswa` kosong
  (tanpa `$fillable`/relasi).
- Route `POST /pengajuan` berada di dalam `role:admin`, padahal dimaksudkan untuk
  form publik.
- Validasi masih di controller (kecuali `PesertaMagangRequest`); logika
  validasi banner duplikat di `store` dan `update`.
- `PengaturanWebsiteController@update` meng-assign 30+ field satu per satu.
- Layout Google Sheets di-hardcode sebagai konstanta controller; nama tab bulan
  memakai `now()->format('F Y')` (bahasa Inggris) dan hanya bulan berjalan;
  rekap absensi dihitung di Blade; tidak ada cache.
- `GoogleSheetsService` melempar exception di constructor bila credential tidak
  ada sehingga sulit di-mock/di-test.
- Dashboard admin & super admin hanya `Route::view` tanpa data.
- Route `superadmin.pengguna.show` terdaftar (dari `Route::resource`) tetapi
  `UserController` tidak punya method `show` → membuka URL tersebut menghasilkan
  error 500. Perlu `->except('show')` atau method redirect (Fase 6).

### Dependensi & tooling
- ~~`composer audit` melaporkan 4 advisory~~ — **diperbaiki** dengan update
  terarah `laravel/framework`, `league/commonmark`, `symfony/http-foundation`.
- `.mcp.json` dari Laravel Boost memanggil `php` dari PATH. PHP default terminal
  masih 8.3 sehingga MCP Boost gagal (dependensi butuh PHP ≥ 8.4.1).
  Solusi yang dipilih: ubah versi PHP default Laragon ke 8.4
  (menu Laragon → PHP → Version), bukan menulis path absolut di `.mcp.json`.
- ~~`AGENTS.md` berisi instruksi bootstrap Boost lama~~ — **diganti** ringkasan
  yang merujuk ke `CLAUDE.md`.

### Keamanan
- Tidak ada rate limiting pada login.
- Super admin dapat menurunkan role akunnya sendiri (hanya hapus yang dicegah).
- Password akun awal di-hardcode di `UserSeeder`.
- Belum ada Policy/Gate, log aktivitas, maupun fitur ubah password sendiri.

### Test
- Hanya ada `ExampleTest` bawaan; belum ada test untuk fitur apa pun.

## 3. Rencana Fase

| Fase | Judul | Status |
|---|---|---|
| 0 | Persiapan & dokumentasi proyek | selesai |
| 1 | Refactor tampilan: layout & komponen Blade bersama | belum |
| 2 | Modul Pengajuan Magang lengkap | belum |
| 3 | Penguatan Absensi Google Sheets | belum |
| 4 | Logbook kegiatan & Penilaian peserta | belum |
| 5 | Dashboard statistik, Laporan & Export | belum |
| 6 | Keamanan, otorisasi & test | belum |
| 7 | Persiapan deploy | belum |
