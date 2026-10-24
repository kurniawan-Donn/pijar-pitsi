STRUKTUR WEBSITE SISTEM INFORMASI MAGANG (MAGENTA)

Project Laravel ada di folder: magenta/

magenta/app/Http/Controllers/
  Admin/          -> fitur khusus Admin (PengajuanMagangController)
  Auth/           -> login & logout (AuthController)
  SuperAdmin/     -> fitur Admin + Super Admin (Banner, Perusahaan,
                     Pengaturan Website, Peserta Magang, Pengguna)
  Website/        -> website publik one-page (HomeController)

magenta/app/Http/Middleware/
  RoleMiddleware  -> pembatasan akses: role:admin, role:super_admin,
                     role:admin,super_admin

magenta/app/Http/Requests/
  PesertaMagangRequest -> validasi tambah/edit peserta magang

magenta/app/Services/
  GoogleSheetsService  -> baca/tulis absensi ke Google Sheets
                          (credential: storage/app/google/service-account.json)

magenta/resources/views/
  website/     -> halaman publik
  auth/        -> login
  admin/       -> dashboard & pengajuan (Admin)
  superadmin/  -> dashboard, banner, pengaturan, perusahaan,
                  peserta-magang, pengguna

Catatan:
- Nama folder controller harus sama persis dengan namespace
  (contoh: SuperAdmin, bukan superAdmin) supaya tetap jalan di server Linux.
