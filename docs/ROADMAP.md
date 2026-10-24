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
- ~~Tidak ada rate limiting pada login~~ — **diperbaiki**: maksimal 5 kali
  gagal per email + IP, lalu dikunci 60 detik (`AuthController`, `LoginTest`).
- Link "Login Admin" dihapus dari navbar website publik; admin masuk langsung
  lewat `/login`.
- Super admin dapat menurunkan role akunnya sendiri (hanya hapus yang dicegah).
- Password akun awal di-hardcode di `UserSeeder`.
- Belum ada Policy/Gate, log aktivitas, maupun fitur ubah password sendiri.

### Test
- Sudah ada test: Kelola Konten, akses halaman dashboard, login, pendaftaran
  publik, kelola pengajuan, absensi Google Sheets, logbook, penilaian,
  statistik dashboard, dan laporan/export (107 test). Belum dites: banner,
  profil perusahaan, CRUD peserta & pengguna.

## Catatan Fase 5 (dashboard, laporan, export)

- Dashboard Admin & Super Admin (`Admin\DashboardController`,
  `SuperAdmin\DashboardController`, data dari `App\Services\StatistikMagang`):
  peserta aktif/selesai/belum dinilai, pengajuan per status & per bulan
  (grafik CSS tanpa library), peserta yang berakhir bulan ini, pengajuan
  terbaru, sebaran predikat, dan ringkasan absensi.
- Ringkasan absensi di dashboard **hanya memakai cache** (tidak memanggil API
  Google) supaya dashboard tetap cepat; rekap lengkap ada di Laporan Absensi.
- Halaman **Laporan** (`superadmin.laporan.index`, Admin & Super Admin):
  rekap peserta per periode, rekap absensi & uang saku per peserta per bulan,
  dan nilai akhir per aspek. Disusun oleh `App\Services\LaporanMagang`.
- Export saat ini tanpa paket tambahan: **CSV** (BOM UTF-8 + pemisah `;`,
  langsung rapi di Excel regional Indonesia) dan **versi cetak** A4 landscape
  (Cetak → Simpan sebagai PDF di browser).
- Hasil cek paket export:
  - `maatwebsite/excel` **tidak kompatibel** dengan Laravel 13 (composer hanya
    menemukan v1.1.5 yang memakai `phpoffice/phpexcel`, sudah ditinggalkan).
  - `barryvdh/laravel-dompdf` v3.1.2 kompatibel.
  - `phpoffice/phpspreadsheet` dan `openspout/openspout` butuh ekstensi PHP
    `zip`, yang di PHP 8.4 Laragon masih nonaktif (`;extension=zip` di
    `php.ini`, file `php_zip.dll` sudah tersedia).

## Catatan Fase 3 (absensi Google Sheets)

- Nama tab bulan **dicocokkan otomatis**: `OKTOBER 2026` (Indonesia) atau
  `OCTOBER 2026` (Inggris), tanpa membedakan huruf besar/kecil. Bila tidak
  ada, halaman menampilkan daftar tab yang tersedia.
- Halaman detail peserta punya **pilihan bulan** (sepanjang periode magang,
  maks. 24 bulan; 6 bulan terakhir bila periode kosong) dan tombol
  **Muat ulang data**. Simpan absensi menulis ke tab bulan yang sedang dipilih.
- Konfigurasi layout sheet, path credential, dan lama cache ada di
  `config/absensi.php` (nilai dari `.env`, lihat `.env.example`).
- Hasil baca di-cache 5 menit (`ABSENSI_CACHE_SECONDS`); cache bulan itu
  dihapus otomatis setelah admin menyimpan absensi.
- Logika ada di `App\Services\AbsensiSheetService` (baca/tulis/cache) dan
  `App\Services\RekapAbsensi` (rekap hadir / izin / sakit / belum diisi —
  izin & sakit sekarang dihitung terpisah).
- `GoogleSheetsService` baru membuat koneksi saat dipakai, sehingga bisa
  di-mock di test; credential hilang memberi pesan khusus.
- Belum diuji dengan spreadsheet asli (credential tidak ada di lingkungan
  pengembangan ini).
- **Koreksi di Fase 4** setelah melihat template asli: baris yang dibaca
  dibatasi 13–43 (tanggal 1–31; footer total/uang saku tidak ikut terbaca),
  dan baris yang hanya berisi nomor + placeholder Action "......" dianggap kosong.

## Catatan Fase 4 (logbook & penilaian)

- **Logbook** dibaca dari Google Sheets (Tanggal, Activity, Output, Lokasi) —
  peserta tidak punya akun login, jadi tidak ada input ganda. Verifikasi
  pembimbing ditulis ke **kolom Action** sheet (Diterima / Ditolak / Review,
  `config('absensi.action_options')`), bisa beberapa hari sekaligus (satu
  panggilan batch API). Tidak ada tabel database untuk logbook. Halaman:
  `superadmin.peserta-magang.logbook`.
- Alasan penolakan belum tersimpan (sheet tidak punya kolom catatan); jejak
  siapa mengubah apa akan dicatat di log aktivitas Fase 6.
- **Rekap kehadiran & uang saku** mengikuti footer template: hari penuh = sesi
  pagi dan siang lengkap, setengah hari = satu sesi lengkap. Tarif dari `.env`
  (`UANG_SAKU_HARI_PENUH=50000`, `UANG_SAKU_SETENGAH_HARI=25000`).
- **Penilaian akhir**: tabel `aspek_penilaians` (6 aspek bawaan, total bobot
  100, diisi oleh migration), `penilaian_pesertas` (satu per peserta) dan
  `penilaian_details` (nama & bobot aspek disalin saat dinilai). Nilai akhir =
  Σ(nilai × bobot) ÷ Σbobot; predikat A ≥ 85, B ≥ 75, C ≥ 65, D ≥ 50, E.
- Admin & Super Admin dapat memverifikasi logbook dan mengisi nilai; hanya
  Super Admin yang mengatur aspek & bobot (menu **Aspek Penilaian**; aspek
  dinonaktifkan, tidak dihapus).
- Detail peserta menampilkan ringkasan logbook bulan terpilih dan nilai akhir;
  daftar peserta punya kolom Nilai Akhir.

## Catatan Fase 2 (pengajuan magang & website)

- Pendaftaran **dua jalur**: form website (utama) + Google Form (tombol hanya
  muncul bila link diisi di Kelola Konten). Pengajuan via Google Form tidak
  masuk database, jadi tidak bisa dicek statusnya di website.
- Form website 3 langkah (data diri, akademik, rencana magang) + unggah surat
  pengantar (PDF/JPG/PNG ≤ 2 MB, disimpan di disk privat
  `storage/app/private/pengajuan`). Dibatasi 3 kiriman/menit per IP.
- Satu NIM tidak bisa punya dua pengajuan berstatus menunggu.
- Cek status publik dengan NIM + email (10 kali/menit per IP), menampilkan
  catatan admin.
- Route publik baru: `pengajuan.store` (dulu `admin.pengajuan.store`, diganti
  karena form ini publik) dan `pengajuan.status`. Route `admin.pengajuan.*`
  sekarang bisa diakses Admin **dan** Super Admin; tambahan `dokumen` dan
  `peserta`.
- Kolom baru: `mahasiswas.asal_kampus`, `pengajuan_magangs.dokumen_path`,
  `pengajuan_magangs.catatan_admin` (catatan admin tidak lagi menimpa
  keterangan pendaftar), `peserta_magangs.pengajuan_magang_id`.
- Data peserta dibuat dari pengajuan **lewat tombol** "Buat Data Peserta" di
  detail pengajuan yang disetujui (bukan otomatis), lalu admin diarahkan ke
  form edit peserta untuk melengkapi pembimbing & link Google Sheets.
- Website publik didesain ulang: navbar sticky + menu mobile, animasi scroll,
  timeline alur, statistik peserta nyata, dan form multi-langkah. Dipecah ke
  `website/partials/*` dan komponen `x-website.field`.
- Zona waktu aplikasi diubah ke `Asia/Jakarta` dan locale ke `id`
  (nama bulan berbahasa Indonesia).

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
| 5 | Dashboard statistik, Laporan & Export | selesai (export .xlsx/.pdf native menunggu keputusan paket) |
| 6 | Keamanan, otorisasi & test | belum |
| 7 | Persiapan deploy | belum |
