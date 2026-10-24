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
- ~~`mahasiswas.email` unik tapi bisa tertimpa NIM lain (error 500)~~ —
  **diperbaiki di Fase 2** (validasi "Email ini sudah terdaftar untuk NIM lain").
- Tabel `absensis` tidak lagi dipakai secara aktif (absensi di Google Sheets).

### Kode
- ~~CSS, sidebar, header, dan logout ditulis ulang di setiap view~~ —
  **diperbaiki di Fase 1** (layout `layouts/dashboard`). Sisa: CSS konten
  masih per halaman (`@push('styles')`) dan banyak kelas mirip antarhalaman
  (`.card`, `.form-control`, `.button`, …) yang bisa disatukan bertahap ke
  komponen saat halaman itu disentuh fase berikutnya.
- `superadmin/banners/create.blade.php` tidak pernah ditampilkan
  (`BannerController@create` me-redirect ke index); bisa dihapus.
- ~~Halaman Edit Pengguna error 500 (parameter route `pengguna` vs `user`)~~ —
  **diperbaiki di Fase 1**.
- ~~View pengajuan kosong, model `Mahasiswa` kosong, route `POST /pengajuan`
  di dalam `role:admin`~~ — **diperbaiki di Fase 2**.
- Validasi masih di controller (kecuali `PesertaMagangRequest`); logika
  validasi banner duplikat di `store` dan `update`.
- `PengaturanWebsiteController@update` meng-assign 30+ field satu per satu.
- ~~Layout sheet di-hardcode, tab bulan hanya bahasa Inggris & bulan berjalan,
  rekap di Blade, tanpa cache, `GoogleSheetsService` sulit di-mock~~ —
  **diperbaiki di Fase 3**.
- ~~Dashboard admin & super admin hanya `Route::view` tanpa data~~ —
  **diperbaiki di Fase 5**.
- ~~Filter tanggal periode membandingkan teks `Y-m-d 00:00:00` dengan
  `Y-m-d` sehingga tanggal batas terlewat~~ — **diperbaiki di Fase 5**
  (`whereDate`). Perhatikan pola ini bila menambah query tanggal baru.
- ~~Route `superadmin.pengguna.show` error 500~~ — **diperbaiki di Fase 6**
  (`->except('show')`).

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
- ~~Tidak ada rate limiting pada login~~ — **diperbaiki**: maksimal 5 kali
  gagal per email + IP, lalu dikunci 60 detik (`AuthController`, `LoginTest`).
- Link "Login Admin" dihapus dari navbar website publik; admin masuk langsung
  lewat `/login`.
- ~~Super admin dapat menurunkan role akunnya sendiri~~ — **diperbaiki di
  Fase 6**; Super Admin terakhir juga tidak bisa diturunkan/dihapus.
- Password akun awal di-hardcode di `UserSeeder`.
- ~~Belum ada log aktivitas & fitur ubah password sendiri~~ — **ada di
  Fase 6**. Policy/Gate menunggu keputusan (lihat Catatan Fase 6).

### Test
- **193 test** (827 assertion). Setiap route aplikasi (56 route) punya test
  akses untuk tamu, admin, dan super admin (`AksesRouteTest`); route baru yang
  belum dimasukkan ke matriks membuat test gagal.
- Coverage kode belum bisa diukur: ekstensi Xdebug/PCOV tidak terpasang.
  Coverage kasar per fitur: semua controller punya feature test; yang paling
  tipis adalah Profil Perusahaan & Kelola Konten (hanya akses + simpan) dan
  Banner (akses + validasi).

## Catatan Fase 6 (keamanan)

- **XSS ditutup**: link tombol banner (dipakai sebagai `href` di website)
  hanya boleh `http(s)://`, path `/…`, atau `#anchor` (`BannerRequest`).
  Link Google Sheets peserta wajib `https://docs.google.com/spreadsheets/d/…`;
  link Google Form wajib http/https.
- Upload banner: hanya JPG/PNG/WEBP ≤ 2 MB (SVG ditolak karena bisa berisi
  script).
- **Proteksi akun**: tidak bisa menurunkan role sendiri; Super Admin terakhir
  tidak bisa diturunkan/dihapus. Password baru minimal 8 karakter berisi huruf
  & angka.
- **Profil Saya** (`profil.*`, klik nama di topbar): ubah nama/email dan ganti
  password (wajib password lama).
- **Log Aktivitas** (`superadmin.log-aktivitas.index`, khusus Super Admin):
  ubah status/hapus pengajuan, buat/hapus peserta, ubah absensi, verifikasi
  logbook, simpan penilaian, ubah aspek, ubah/hapus pengguna, hapus banner,
  ganti password. Nama pelaku disalin supaya log tetap terbaca bila akun
  dihapus.
- Pesan validasi bawaan Laravel kini berbahasa Indonesia (`lang/id/validation.php`).
- Seluruh kode dirapikan dengan Pint (commit terpisah).
- Rate limiting login sudah ada sejak sebelum Fase 2.
- **Belum**: Policy/Gate (menunggu persetujuan), password seeder masih
  hard-code (Fase 7).

## Catatan Fase 1 (layout & komponen)

- Layout: `resources/views/layouts/dashboard.blade.php` (section `title`,
  `page_title`, `page_subtitle`, `content`; stack `styles`, `scripts`).
- Sidebar: `layouts/partials/dashboard-sidebar.blade.php`, menu sesuai role
  (Kelola Pengguna hanya Super Admin). Tambah menu baru di array `$menuSections`.
- CSS bersama: `layouts/partials/dashboard-styles.blade.php` (partial inline,
  bukan Vite, karena `app.css` memuat reset Tailwind yang akan mengubah tampilan
  dan supaya deploy tidak bergantung pada `npm run build`).
- Komponen: `<x-alert>`, `<x-flash-messages>`, `<x-stat-card>`, `<x-button>`,
  `<x-form.input>`.
- Sidebar & topbar disamakan memakai desain halaman Dashboard; sebelumnya ada
  5 varian berbeda dan menu "Data Peserta" hanya muncul di halaman peserta.
- Total baris view admin/super admin: 23.257 → 14.386 (termasuk 931 baris
  layout & komponen baru).

## 3. Rencana Fase

| Fase | Judul | Status |
|---|---|---|
| 0 | Persiapan & dokumentasi proyek | selesai |
| 1 | Refactor tampilan: layout & komponen Blade bersama | selesai |
| 2 | Modul Pengajuan Magang lengkap (+ redesain website) | selesai |
| 3 | Penguatan Absensi Google Sheets | selesai |
| 4 | Logbook kegiatan & Penilaian peserta | selesai |
| 5 | Dashboard statistik, Laporan & Export | selesai |
| 6 | Keamanan, otorisasi & test | selesai (Policy/Gate menunggu keputusan) |
| 7 | Persiapan deploy | belum |
