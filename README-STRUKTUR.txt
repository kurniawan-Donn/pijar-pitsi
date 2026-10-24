STRUKTUR WEBSITE SISTEM INFORMASI MAGANG (MAGENTA)

Repository ini adalah project Laravel MAGENTA (root = folder project).

app/Http/Controllers/
  Admin/          -> fitur khusus Admin (PengajuanMagangController)
  Auth/           -> login & logout (AuthController)
  SuperAdmin/     -> fitur Admin + Super Admin (Banner, Perusahaan,
                     Pengaturan Website, Peserta Magang, Pengguna)
  Website/        -> website publik one-page (HomeController) dan
                     pendaftaran + cek status (PendaftaranController)

app/Http/Middleware/
  RoleMiddleware  -> pembatasan akses: role:admin, role:super_admin,
                     role:admin,super_admin

app/Http/Requests/
  PesertaMagangRequest         -> validasi tambah/edit peserta magang
  StorePengajuanMagangRequest  -> validasi form pendaftaran website
  UpdateStatusPengajuanRequest -> validasi ubah status pengajuan
  CekStatusPengajuanRequest    -> validasi cek status (NIM + email)

app/Services/
  GoogleSheetsService  -> baca/tulis absensi ke Google Sheets
                          (credential: storage/app/google/service-account.json)

resources/views/
  layouts/     -> dashboard.blade.php (layout Admin & Super Admin)
                  partials/dashboard-sidebar (menu sesuai role)
                  partials/dashboard-styles  (CSS bersama)
  components/  -> alert, flash-messages, stat-card, button, form/input,
                  pagination, status-badge, website/field
  website/     -> halaman publik (index + partials: styles, scripts,
                  pendaftaran, cek-status)
  auth/        -> login
  admin/       -> dashboard & pengajuan (Admin)
  superadmin/  -> dashboard, banner, pengaturan, perusahaan,
                  peserta-magang, pengguna

Catatan:
- Halaman dashboard baru cukup memakai @extends('layouts.dashboard') dan
  mengisi section title, page_title, page_subtitle, content; CSS khusus
  halaman lewat @push('styles').
- Nama folder controller harus sama persis dengan namespace
  (contoh: SuperAdmin, bukan superAdmin) supaya tetap jalan di server Linux.
