STRUKTUR WEBSITE SISTEM INFORMASI MAGANG (PILARA)

Repository ini adalah project Laravel PILARA (root = folder project).
Rencana fitur & keputusan: docs/rencana-fitur.md; catatan per fase: docs/ROADMAP.md.

AKTOR
  admin        -> mengelola pengajuan, peserta, logbook, penilaian, laporan
  super_admin  -> semua fitur admin + kelola pengguna, aspek penilaian, log aktivitas
  peserta      -> anak magang: biodata, logbook harian, izin/sakit, feedback,
                  surat keterangan (akun dibuat admin dari halaman peserta)

app/Http/Controllers/
  Admin/          -> dashboard Admin, pengajuan magang
  Auth/           -> login & logout (AuthController), profil & password (ProfilController)
  Peserta/        -> area peserta: Dashboard, Biodata, Logbook, Izin, Feedback, Surat
  SuperAdmin/     -> fitur Admin + Super Admin: Banner, Perusahaan, Pengaturan Website,
                     Peserta Magang, Akun Peserta, Biodata, Penempatan, Logbook Harian,
                     Impor Logbook, Penilaian, Aspek Penilaian, Feedback, Surat Keterangan,
                     Monitoring, Laporan, Pengaturan Magang, Divisi, Pengguna, Log Aktivitas
  Website/        -> website publik one-page (HomeController) dan
                     pendaftaran + cek status (PendaftaranController)

app/Http/Middleware/
  RoleMiddleware      -> pembatasan akses: role:admin, role:super_admin, role:peserta, ...
  WajibGantiPassword  -> akun dengan password sementara hanya bisa membuka profil
  PesertaAktif        -> area peserta: akun aktif & terhubung data peserta;
                         peserta selesai hanya baca (kecuali feedback)

app/Policies/
  LogbookHarianPolicy, IzinPesertaPolicy -> bukti hanya untuk pemilik & admin

app/Services/
  PeriodeMagang          -> periode bulanan 28–27 (tanggal awal dari Pengaturan Magang)
  KalenderLogbook        -> tabel logbook per periode, kunci tanggal, rekap & uang saku
  RekapAbsensi           -> aturan hari penuh / setengah hari, normalisasi jam
  CetakLogbook           -> PDF rekap logbook periode (format sheet bulanan)
  ParserSheetLogbook     -> baca tab sheet lama (format Juni–Agustus & September dst.)
  ImporRiwayatLogbook    -> impor riwayat dari Google Sheets peserta / file .xlsx
  GoogleSheetsService    -> koneksi API Google Sheets (hanya untuk impor)
                            credential: storage/app/google/service-account.json
  MonitoringPeserta      -> data halaman Monitoring & kartu dashboard
  StatistikMagang        -> angka pengajuan, peserta, predikat untuk dashboard
  LaporanMagang          -> isi laporan (peserta, kehadiran & uang saku, nilai, feedback)
  RekapFeedback          -> rata-rata & jawaban feedback
  CetakSuratKeterangan   -> PDF surat keterangan magang

config/
  absensi.php   -> path credential Google Sheets, tarif uang saku awal
  feedback.php  -> pertanyaan feedback magang

resources/views/
  layouts/     -> dashboard.blade.php (layout semua role)
                  partials/dashboard-sidebar (menu sesuai role)
                  partials/dashboard-styles  (CSS bersama)
  components/  -> alert, flash-messages, stat-card, button, form/input,
                  pagination, status-badge, website/field
  website/     -> halaman publik (index + partials)
  auth/, profil/
  admin/       -> dashboard & pengajuan (Admin)
  peserta/     -> dashboard, biodata, logbook, izin, feedback, surat
  superadmin/  -> dashboard, banner, pengaturan, perusahaan, peserta-magang
                  (detail, biodata, logbook-harian, penilaian, surat), pengaturan-magang,
                  divisi, monitoring, feedback, laporan, aspek-penilaian, pengguna,
                  log-aktivitas
  logbook/     -> tabel & rekap logbook bersama, template PDF logbook
  penilaian/   -> template PDF lembar penilaian
  surat/       -> template PDF surat keterangan

Catatan:
- Halaman dashboard baru cukup memakai @extends('layouts.dashboard') dan
  mengisi section title, page_title, page_subtitle, content; CSS khusus
  halaman lewat @push('styles').
- Nama folder controller harus sama persis dengan namespace
  (contoh: SuperAdmin, bukan superAdmin) supaya tetap jalan di server Linux.
- File unggahan (dokumen pengajuan, bukti logbook, bukti izin) disimpan di disk
  privat "local" (storage/app/private) dan hanya diunduh lewat route yang dicek.
- Data sensitif biodata (NIK, rekening, riwayat kesehatan) terenkripsi dengan
  APP_KEY: jangan mengganti APP_KEY di server setelah ada data.
