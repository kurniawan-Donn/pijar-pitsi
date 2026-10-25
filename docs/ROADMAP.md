# Roadmap Pengembangan MAGENTA

Dokumen ini merangkum kondisi project saat ini, utang teknis yang ditemukan,
dan rencana pengembangan per fase (lihat juga `docs/PROMPT-PENGEMBANGAN.md`).

## 1. Fitur yang Sudah Ada

Ringkasan kondisi setelah Fase 18 (detail per fase ada di catatan di bawah;
struktur file di `README-STRUKTUR.txt`).

### Website publik
- Halaman one-page (banner, profil perusahaan, konten), pendaftaran magang
  lewat form website atau Google Form, cek status pengajuan (MG-xxxxxx).

### Aktor
- **Admin** dan **Super Admin** (Super Admin: + kelola pengguna, aspek
  penilaian, log aktivitas lewat Gate).
- **Peserta** (anak magang): akun dibuat admin, password sementara wajib
  diganti; peserta selesai hanya baca (kecuali feedback).

### Admin & Super Admin
- Pengajuan magang → buat data peserta; akun login peserta.
- Pengaturan Magang (batas isi mundur, periode 28–27, tarif uang saku
  berjangka, hari libur, penanda tangan, format nomor surat), Divisi dan
  penempatan per periode.
- Biodata (tinjau/setujui/revisi), Logbook Harian (verifikasi, izin/sakit,
  buka kunci, PDF periode, impor riwayat sheet lama), Penilaian (Hard/Soft
  Skill, predikat teks, PDF), Feedback (rekap), Surat Keterangan (terbit,
  nomor otomatis, PDF).
- Monitoring, dashboard statistik, Laporan (peserta, kehadiran & uang saku
  per periode, nilai, feedback) dengan export XLSX/PDF/CSV/cetak.

### Peserta
- Dashboard, Biodata, Logbook Harian (+ izin/sakit, bukti, PDF), Feedback
  (dibuka H-30), Surat Keterangan (unduh setelah terbit), Profil.

### Sumber data
- Semua kehadiran, logbook, dan uang saku dari **database**. Google Sheets
  hanya dipakai untuk **impor sekali** riwayat buku kerja lama
  (`google_sheet_url` peserta + credential service account).

### Data utama
| Tabel | Isi |
|---|---|
| users | akun admin/super_admin/peserta (`role` string, `aktif`, `wajib_ganti_password`) |
| mahasiswas, pengajuan_magangs | pendaftar & pengajuan |
| peserta_magangs | peserta (`user_id`, `nomor_peserta`, periode, status, `google_sheet_url` untuk impor) |
| biodata_pesertas | biodata (NIK, rekening, kesehatan terenkripsi) |
| pengaturan_magangs, tarif_uang_sakus, hari_liburs | pengaturan program |
| divisis, penempatan_pesertas | divisi & rotasi per periode |
| logbook_harians, logbook_buktis, izin_pesertas, buka_kunci_logbooks | logbook & kehadiran |
| kelompok_penilaians, aspek_penilaians, predikat_penilaians, penilaian_pesertas, penilaian_details | penilaian |
| feedback_pesertas, surat_keterangans | feedback & surat |
| log_aktivitas | jejak aksi |
| absensis | tabel lama, tidak dipakai (bisa dihapus di fase terpisah) |

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
- ~~Belum ada log aktivitas, ubah password sendiri, maupun Gate~~ — **ada di
  Fase 6**.

### Test
- **370 test** (1883 assertion). Setiap route aplikasi (106 route) punya test
  akses untuk tamu, admin, super admin, dan peserta (`AksesRouteTest`); route
  baru yang belum dimasukkan ke matriks membuat test gagal.
- Coverage kode belum bisa diukur: ekstensi Xdebug/PCOV tidak terpasang.
  Coverage kasar per fitur: semua controller punya feature test; yang paling
  tipis adalah Profil Perusahaan & Kelola Konten (hanya akses + simpan) dan
  Banner (akses + validasi).

## Catatan Fase 18 (laporan dari database)

- Sesuai keputusan "impor sekali, lalu Google Sheets tidak dibaca lagi":
  - **Laporan "Kehadiran & Uang Saku"** (jenis `absensi`) kini per periode
    28–27 dari logbook harian: hari penuh, setengah hari, izin, sakit, libur,
    belum diisi, menunggu verifikasi, uang saku (tarif per tanggal), divisi
    periode itu, dan rekening. Filter `bulan` diganti `periode` (Y-m).
  - **Dashboard**: panel absensi dari cache sheet diganti "Kehadiran Periode
    …" dari data monitoring (dihitung sekali bersama kartu monitoring).
  - **Detail peserta**: bagian absensi & daily activity Google Sheets dihapus;
    kartu sheet menjadi "Google Sheets (Riwayat)" dengan tombol impor.
- Dihapus: `AbsensiSheetService`, `Logbook` (service sheet),
  `TabBulanTidakDitemukanException`, `SuperAdmin/LogbookController` + view,
  `MemilihBulanAbsensi`, `VerifikasiLogbookRequest`, route
  `superadmin.peserta-magang.absensi.update/.refresh` dan
  `superadmin.peserta-magang.logbook(.update)`, beserta test-nya.
  Penggantinya: Logbook Harian & Izin (`superadmin.peserta-magang.logbook-harian`).
- `RekapAbsensi` disisakan untuk aturan jam kerja; `config/absensi.php` hanya
  berisi credential (untuk impor) dan tarif awal; variabel `ABSENSI_*`
  dihapus dari `.env.example` (boleh dihapus juga dari `.env`).
- Label aksi log lama (`ubah_absensi`, `verifikasi_logbook`) tetap ada agar
  riwayat Log Aktivitas terbaca.

## Catatan Fase 17 (surat keterangan magang)

- Tabel `surat_keterangans` (satu per peserta): status draft/terbit, nomor
  unik, urut per tahun, tanggal surat, opsi cantumkan nilai, salinan data
  (JSON) saat terbit.
- **Draft otomatis** saat status peserta berubah menjadi `selesai` (event
  `saved` di `PesertaMagang`); peserta yang sudah selesai sebelumnya bisa
  dibuatkan draft dari halaman surat.
- **Syarat terbit**: status selesai, biodata disetujui, penilaian diisi,
  feedback dikirim (`SuratKeterangan::syaratBelumTerpenuhi()`).
- **Nomor** dari format Pengaturan Magang (`{urut}`, `{bulan_romawi}`,
  `{bulan}`, `{tahun}`), urut 3 digit per tahun tanggal surat, diberikan sekali;
  terbit ulang memakai nomor yang sama dengan data terbaru.
- PDF (`surat/cetak`, `App\Services\CetakSuratKeterangan`): kop perusahaan,
  data peserta, periode, divisi yang pernah ditempati, nilai & predikat
  (opsional), kota/tanggal, penanda tangan "Menyetujui". Draft diberi tanda
  DRAFT dan memakai data terkini.
- Admin: `superadmin.peserta-magang.surat*`. Peserta: menu Surat Keterangan
  (`peserta.surat.*`), unduh hanya setelah terbit.
- Setelah terbit, **feedback peserta terkunci**.
- Kop surat memakai Profil Perusahaan; halaman surat memberi peringatan bila
  nama perusahaan / penanda tangan / kota surat belum diisi.

## Catatan Fase 16 (penyesuaian penilaian)

- Struktur mengikuti sheet PENILAIAN MHS MAGANG dan bisa diatur Super Admin
  (menu Aspek Penilaian):
  - `kelompok_penilaians` (bawaan Hard Skill 60%, Soft Skill 40%; total 100%);
  - aspek per kelompok (7 Hard Skill + 8 Soft Skill dari buku kerja); aspek
    lama 6 buah dinonaktifkan, tidak dihapus;
  - `predikat_penilaians` (Perlu Pembinaan ≥0, Cukup ≥20,01, Baik ≥40,01,
    Memuaskan ≥60,01, Sangat Memuaskan ≥80,01) beserta keterangannya.
- Rumus: nilai kelompok = rata-rata aspeknya; nilai akhir =
  Σ(rata-rata kelompok × bobot) ÷ Σbobot. Skala 1–100.
- `penilaian_pesertas.predikat` kini teks; kolom kesimpulan `kekuatan`,
  `aspek_perbaikan`, `langkah_perbaikan`. `penilaian_details` menyalin nama
  kelompok & bobotnya. Penilaian lama (huruf A–E, catatan pembimbing) tetap
  tampil apa adanya.
- Lembar penilaian PDF (`superadmin.peserta-magang.penilaian.cetak`, view
  `penilaian/cetak`): identitas, bobot, tabel predikat, indikator per
  kelompok, grand total, kesimpulan, kalimat hasil, tanda tangan.
- Sebaran predikat di dashboard mengikuti tabel predikat.

## Catatan Fase 15 (feedback magang)

- Pertanyaan mengikuti sheet FEEDBACK MHS MAGANG dan disimpan di
  `config/feedback.php`: 10 skala 1–5, 4 pertanyaan terbuka, kesediaan
  merekomendasikan (4 pilihan), evaluasi keseluruhan, pernyataan kejujuran.
  Jangan mengubah urutan/jumlah pertanyaan skala setelah ada jawaban.
- Tabel `feedback_pesertas` (satu per peserta, skor JSON, `jumlah_kirim`).
- Peserta (`peserta.feedback.*`): menu setelah Logbook, tampil 🔒 dan
  terkunci sampai sebulan sebelum tanggal selesai magang; semua pertanyaan
  wajib; bisa diedit & dikirim ulang. Peserta berstatus selesai tetap boleh
  mengirim feedback (pengecualian di middleware `PesertaAktif`). Penguncian
  setelah surat keterangan terbit menyusul di Fase 17.
- Admin: menu **Feedback Magang** (`superadmin.feedback.index`) berisi rata-rata
  per pertanyaan, sebaran rekomendasi, daftar per peserta, dan jawaban
  terbuka (filter tanggal selesai magang); detail per peserta
  (`superadmin.peserta-magang.feedback`); jenis laporan `feedback` untuk
  export CSV/XLSX/PDF/cetak.

## Catatan Fase 14 (dashboard monitoring)

- `App\Services\MonitoringPeserta` (semua dari database): tabel per peserta
  untuk periode berjalan (divisi & mentor, progres biodata, hari terisi,
  hari kerja belum diisi, isi hari ini, penuh/setengah/izin/sakit, menunggu
  verifikasi, uang saku sementara, tanggal terakhir isi), ringkasan peserta
  aktif, dan riwayat logbook terbaru.
- Halaman **Monitoring** (`superadmin.monitoring.index`, menu Utama, admin +
  super admin): filter status, divisi periode ini, cari, "belum isi hari
  ini", urutan (belum diisi / menunggu / progres biodata).
- Kedua dashboard menampilkan 4 kartu monitoring (logbook hari ini,
  logbook & izin menunggu, biodata menunggu tinjau) yang menaut ke halaman
  Monitoring dengan filter yang sesuai.
- Hari libur (Sabtu/Minggu/hari libur) dan hari di luar masa magang tidak
  dihitung "wajib isi hari ini".
- Monitoring menghitung rekap per peserta setiap kali dibuka; cukup untuk
  puluhan peserta, perlu cache bila peserta aktif ratusan.

## Catatan Fase 13 (impor riwayat sheet lama)

- `App\Services\ParserSheetLogbook` membaca satu tab bulanan; posisi kolom
  dicari dari baris judul, jadi format lama (Keterangan + Paraf) dan format
  baru (Activity, Output, Lokasi, Action) sama-sama terbaca. Tab lain
  (profil, penilaian, feedback) dilewati.
- Pembersihan jam: "08.00", "07:56", "8;10", sel waktu Excel, pecahan hari
  dari Google Sheets. Jam bertitik yang diubah Excel menjadi tanggal
  (mis. "08.04" → 8 April) ditafsirkan kembali menjadi jam dan diberi
  peringatan; isi yang tidak terbaca ("??") dikosongkan dan dilaporkan.
- `App\Services\ImporRiwayatLogbook`: impor dari Google Sheets peserta
  (`UNFORMATTED_VALUE` + `SERIAL_NUMBER`) atau upload `.xlsx` (OpenSpout),
  `sumber = impor`. Tanggal yang sudah ada tidak ditimpa; hanya masa magang.
  "Hapus hasil impor" menghapus hari impor tanpa bukti (untuk impor ulang).
- Panel impor + laporan hasil (per tab, dilewati, peringatan) di halaman
  logbook harian admin.
- Diuji dengan buku kerja contoh: rekap Juni Rp850.000, Juli Rp1.000.000,
  September Rp1.000.000 sama dengan footer Excel; Agustus berkurang 2 hari
  karena jamnya "??" di sheet (perlu dikoreksi admin).

## Catatan Fase 12 (logbook harian, kehadiran, izin, uang saku)

- **Aturan jam kerja baru** (`RekapAbsensi::jenisKehadiran`, juga untuk
  sheet lama): hari penuh = pagi masuk + siang keluar (jam istirahat boleh
  kosong); setengah hari = hanya sesi pagi atau hanya sesi siang lengkap.
- Tabel `logbook_harians` (format sheet September + kehadiran
  hadir/izin/sakit/libur + status verifikasi), `logbook_buktis` (maks. 3 file
  per hari, disk privat `local`), `izin_pesertas` (satu bukti untuk satu
  rentang), `buka_kunci_logbooks`.
- `App\Services\KalenderLogbook`: tabel per periode 28–27, akses tanggal
  (hari ini & mundur sesuai batas di Pengaturan Magang; belum tiba terkunci;
  tanggal yang dibuka admin), libur otomatis (Sabtu/Minggu + `hari_liburs`),
  rekap (penuh, setengah, izin, sakit, libur, belum diisi, ditolak) dan uang
  saku per hari dengan tarif yang berlaku di tanggal itu. Hari hadir yang
  `ditolak` tidak dibayar; `menunggu`/`review` tetap dihitung.
- Peserta (`peserta.logbook.*`, `peserta.izin.*`): isi/ubah/hapus per hari,
  upload bukti, ajukan izin/sakit beberapa hari (hanya hari kerja; hari yang
  sudah diisi hadir tidak ditimpa; bukti wajib untuk sakit), batalkan izin
  yang belum diverifikasi, unduh PDF rekap periode.
- Admin (`superadmin.peserta-magang.logbook-harian*`): verifikasi beberapa
  hari sekaligus (Diterima/Review/Ditolak + catatan), terima/tolak izin
  (diteruskan ke semua harinya), buka kunci tanggal, unduh PDF format sheet
  bulanan (`App\Services\CetakLogbook`, view `logbook/cetak`).
- Bukti hanya bisa dibuka pemiliknya dan admin (`LogbookHarianPolicy`,
  `IzinPesertaPolicy`).
- Halaman logbook Google Sheets lama tetap ada sebagai riwayat sampai impor
  (Fase 13).

## Catatan Fase 11 (biodata peserta)

- Tabel `biodata_pesertas` mengikuti sheet PROFIL MAHASISWA + rekening uang
  saku. NIK, nomor rekening, dan riwayat kesehatan memakai cast `encrypted`
  (**`APP_KEY` di server tidak boleh diganti** setelah ada data, catat di
  DEPLOY). Keterampilan, bahasa, dan riwayat kesehatan berupa daftar (JSON).
- Peserta (`peserta.biodata.*`): form 6 bagian, simpan draft kapan saja,
  kirim bila semua field wajib (`BiodataPeserta::FIELD_WAJIB`) terisi dan
  pernyataan dicentang. Form terisi otomatis dari data pendaftaran.
- Alur status: draft → diajukan → disetujui / perlu_revisi. Biodata terkunci
  saat diajukan & disetujui; admin bisa membuka kembali dengan meminta revisi.
- Admin (`superadmin.peserta-magang.biodata*`): lihat biodata (NIK
  disamarkan, klik untuk tampilkan), isi detail program (posisi, jenis
  program, durasi, mentor; terkunci untuk peserta), setujui/minta revisi.
  Saat disetujui, nama, NIM, dan asal kampus peserta disalin dari biodata.
- Progres biodata (persen field wajib) tampil di dashboard peserta, detail
  peserta, dan halaman biodata admin.

## Catatan Fase 10 (pengaturan magang)

- Halaman **Pengaturan Magang** (`superadmin.pengaturan-magang.*`, admin +
  super admin): batas isi logbook mundur (bawaan 7 hari), tanggal awal
  periode (bawaan 28), kota surat, penanda tangan Mengetahui/Menyetujui, dan
  format nomor surat keterangan. Satu baris di tabel `pengaturan_magangs`.
- **Tarif uang saku** pindah dari `.env` ke tabel `tarif_uang_sakus` (riwayat
  dengan `berlaku_mulai`). `TarifUangSaku::berlakuPada($tanggal)` memilih tarif
  yang berlaku; nilai `.env` hanya tarif awal saat migration. Detail peserta,
  laporan, dan dashboard sudah memakai tarif dari database (untuk absensi
  Google Sheets dipakai tarif yang berlaku di awal bulan). Tarif terakhir
  tidak bisa dihapus.
- **Hari libur** nasional/cuti bersama (`hari_liburs`), bisa ditambah per
  rentang (maks. 31 hari); tanggal yang sudah ada dilewati.
- `App\Services\PeriodeMagang`: periode 28–27 (kunci `Y-m` = bulan tanggal
  akhir), dipotong di tanggal mulai/selesai magang; tanggal awal 1 = bulan
  kalender. Di-bind di container dengan tanggal awal dari pengaturan.
- **Divisi** (`divisis`, menu Divisi) dan **penempatan per periode**
  (`penempatan_pesertas`) di detail peserta: divisi + mentor per periode,
  periode kosong mengikuti periode sebelumnya (`PesertaMagang::penempatanUntuk()`).
  Divisi yang sudah dipakai tidak bisa dihapus, hanya dinonaktifkan.
- Semua perubahan tercatat di Log Aktivitas.

## Catatan Fase 9 (kelola akun peserta)

- Kartu **Akun Login Peserta** di halaman detail peserta
  (`SuperAdmin/AkunPesertaController`, route `superadmin.peserta-magang.akun.*`,
  admin + super admin): buat akun, reset password, aktifkan/nonaktifkan, hapus.
- Email login terisi otomatis dari email pendaftaran (pengajuan) bila ada.
- Password sementara 10 karakter (huruf + angka) hanya ditampilkan sekali
  lewat flash session, tidak pernah dicatat di log, dan wajib diganti saat
  login pertama.
- Semua aksi tercatat di Log Aktivitas (`buat_akun_peserta`,
  `reset_password_peserta`, `ubah_status_akun_peserta`, `hapus_akun_peserta`).
- Menghapus akun tidak menghapus data peserta; menghapus data peserta ikut
  menghapus akunnya.

## Catatan Fase 8 (aktor peserta)

Rencana lengkap fitur peserta (biodata, logbook, uang saku, feedback, surat
keterangan) ada di `docs/rencana-fitur.md`. Fase 8 hanya fondasinya:

- Role baru `peserta` di tabel `users`. Kolom `role` diubah dari enum ke
  string supaya role baru tidak perlu mengubah enum di tiap database; nilai
  valid ada di `User::ROLES`.
- `peserta_magangs.user_id` menghubungkan akun dan data peserta (satu akun
  per peserta, `nullOnDelete`). `nomor_peserta` dibuat otomatis
  (`PesertaMagang::nomorPesertaBaru()`), peserta lama diisi oleh migration.
- Area `/peserta` (`peserta.*`) dijaga `role:peserta` + `peserta.aktif`:
  akun nonaktif dikeluarkan, akun tanpa data peserta 403, peserta berstatus
  selesai hanya boleh request baca (GET).
- Semua route lama tetap menolak peserta (403), dibuktikan `AksesRouteTest`.
  Profil & ganti password dibuka untuk peserta.
- Kelola Pengguna hanya menampilkan akun admin/super admin; akun peserta
  menghasilkan 404 di sana (dikelola dari halaman peserta di Fase 9).
- `docs/reverensi/` (contoh buku kerja berisi data pribadi) masuk `.gitignore`.

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
- **Gate** (bukan Policy per model, karena aturan akses murni berbasis role):
  `kelola-pengguna`, `kelola-aspek-penilaian`, `lihat-log-aktivitas` di
  `AppServiceProvider::configureGates()`. Dipakai di route (`can:`) sebagai
  lapisan kedua di samping `role:super_admin`, dan di sidebar (kunci `can`).
  Policy per model baru diperlukan bila ada aturan per data, misalnya admin
  hanya boleh mengelola peserta bimbingannya.
- **Belum**: password akun awal di seeder masih hard-code (Fase 7).

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
| 6 | Keamanan, otorisasi & test | selesai |
| 7 | Persiapan deploy (dikerjakan setelah Fase 18) | belum |
| 8 | Aktor peserta: role, akun, middleware | selesai |
| 9 | Kelola akun peserta oleh admin | selesai |
| 10 | Pengaturan magang, tarif, hari libur, periode 28–27, divisi | selesai |
| 11 | Biodata peserta | selesai |
| 12 | Logbook harian, kehadiran, izin, uang saku | selesai |
| 13 | Impor riwayat sheet lama | selesai |
| 14 | Dashboard monitoring | selesai |
| 15 | Feedback magang | selesai |
| 16 | Penyesuaian penilaian | selesai |
| 17 | Surat keterangan magang | selesai |
| 18 | Laporan dari database | selesai |
