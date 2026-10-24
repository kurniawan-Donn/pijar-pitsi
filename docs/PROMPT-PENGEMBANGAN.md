# Prompt Pengembangan MAGENTA per Fase

Cara pakai:
1. Buka **new chat** Claude Code di folder project ini.
2. Kirim prompt **satu fase per chat**, berurutan mulai dari Fase 0.
3. Setelah satu fase selesai (test lulus, sudah di-commit dan di-merge), baru lanjut ke fase berikutnya.
4. Setiap prompt sudah berisi konteks yang dibutuhkan, jadi bisa langsung di-copy apa adanya.

> Tips: Untuk fase besar (1–5), aktifkan **Plan mode** dulu supaya Claude menyusun rencana dan Anda bisa menyetujuinya sebelum kode diubah.

---

## Fase 0 — Persiapan & Dokumentasi Proyek

```text
Kamu bekerja di project Laravel "MAGENTA" (Sistem Informasi Magang) di folder ini.

Konteks singkat:
- Laravel 13, PHP 8.4. PHP default di terminal masih 8.3, jadi gunakan PHP 8.4 dari Laragon:
  C:\laragon\bin\php\php-8.4\php.exe (untuk artisan maupun composer.phar).
- Role pengguna: admin dan super_admin (middleware `role:...` di app/Http/Middleware/RoleMiddleware.php).
- Controller dikelompokkan: Admin/, Auth/, SuperAdmin/, Website/.
- Absensi peserta dibaca/ditulis ke Google Sheets lewat app/Services/GoogleSheetsService.php.
- Struktur dijelaskan di README-STRUKTUR.txt.

Tugas fase ini (belum menambah fitur):
1. Pastikan `composer install`, `php artisan migrate --seed` (database sqlite), `php artisan route:list`, dan `php artisan test` berjalan. Laporkan jika ada yang gagal.
2. Ikuti instruksi di CLAUDE.md untuk memasang Laravel Boost (dev dependency), lalu baca ulang AGENTS.md/CLAUDE.md yang baru.
3. Buat file docs/ROADMAP.md berisi: ringkasan fitur yang SUDAH ada (baca routes/web.php, controller, model, migration), daftar utang teknis yang kamu temukan, dan daftar fase 1–7 di bawah ini dengan status "belum".
4. Tambahkan bagian "Konteks Proyek" di CLAUDE.md (setelah bagian Boost) yang merangkum aturan proyek: bahasa Indonesia untuk teks UI dan komentar, path PHP 8.4, nama route tidak boleh diubah tanpa alasan, dan wajib menjalankan test sebelum commit.

Fase berikutnya (untuk ROADMAP):
1. Refactor tampilan: layout & komponen Blade bersama
2. Modul Pengajuan Magang lengkap
3. Penguatan Absensi Google Sheets
4. Logbook kegiatan & Penilaian peserta
5. Dashboard statistik, Laporan & Export
6. Keamanan, otorisasi & test
7. Persiapan deploy

Aturan: kerjakan di branch baru `fase-0-persiapan`, commit dengan pesan berbahasa Indonesia, jangan push. Di akhir, ringkas apa yang sudah dilakukan dan apa yang perlu saya putuskan.
```

---

## Fase 1 — Refactor Tampilan (Layout & Komponen Blade)

```text
Baca CLAUDE.md dan docs/ROADMAP.md dulu. Ini Fase 1.

Masalah: setiap view di resources/views (admin/*, superadmin/*) panjangnya 1.500–2.500 baris karena CSS, sidebar, header, dan tombol logout ditulis ulang di setiap file.

Tugas:
1. Buat layout bersama, misalnya resources/views/layouts/dashboard.blade.php (untuk admin & super admin), dengan sidebar yang menampilkan menu sesuai role pengguna yang login (cek route yang ada di routes/web.php).
2. Pindahkan CSS yang sama ke satu tempat (resources/css/app.css lewat Vite, atau satu file partial). Jangan mengubah tampilan visual: hasil akhir harus terlihat sama.
3. Buat komponen Blade untuk elemen berulang: alert sukses/error, kartu statistik, tombol, form input dengan pesan error validasi.
4. Ubah semua view dashboard, banners, pengaturan, perusahaan, peserta-magang, dan pengguna supaya memakai layout & komponen tersebut. Halaman website publik (website/index) dan login boleh punya layout sendiri.
5. Kerjakan bertahap per folder view, dan setelah tiap folder jalankan `php artisan test` serta `php artisan view:cache` untuk memastikan tidak ada error Blade.
6. Bila memungkinkan, buka halaman di browser (php artisan serve) dan cek tampilan setiap halaman, login dengan akun dari database/seeders/UserSeeder.php.

Aturan: branch `fase-1-layout`, jangan ubah nama route maupun logika controller, commit per langkah. Update status Fase 1 di docs/ROADMAP.md. Di akhir, laporkan berapa baris yang berkurang dan halaman mana yang perlu saya cek manual.
```

---

## Fase 2 — Modul Pengajuan Magang Lengkap

```text
Baca CLAUDE.md dan docs/ROADMAP.md dulu. Ini Fase 2.

Kondisi sekarang:
- app/Http/Controllers/Admin/PengajuanMagangController.php sudah punya index, store, show, updateStatus, destroy.
- View resources/views/admin/pengajuan/index.blade.php dan show.blade.php masih KOSONG.
- Route /pengajuan (store) ada di dalam middleware role:admin, padahal komentar di controller menyebut "Simpan pengajuan dari website utama".
- Model Mahasiswa masih kosong (tanpa $fillable/relasi).
- Pendaftaran publik saat ini memakai Google Form (kolom google_form_url di pengaturan_websites).

Tugas:
1. Tanyakan dulu ke saya: apakah pendaftaran tetap via Google Form, atau diganti form pendaftaran di website sendiri (atau keduanya)? Tunggu jawaban sebelum mengubah alur.
2. Lengkapi model Mahasiswa ($fillable, relasi hasMany ke PengajuanMagang) dan relasi kebalikannya.
3. Buat view daftar pengajuan (tabel, filter status menunggu/disetujui/ditolak, pencarian nama/NIM, pagination) dan detail pengajuan (data mahasiswa, perusahaan, tombol ubah status dengan keterangan, hapus dengan konfirmasi). Gunakan layout dari Fase 1.
4. Tambahkan menu Pengajuan di sidebar admin (dan super admin bila perlu, diskusikan dulu).
5. Saat status diubah menjadi "disetujui", tawarkan pembuatan data PesertaMagang otomatis dari data pengajuan (konfirmasi dulu ke saya rancangannya).
6. Pindahkan validasi ke FormRequest (contoh: app/Http/Requests/PesertaMagangRequest.php).
7. Tulis feature test: daftar, detail, ubah status, hapus, dan akses ditolak untuk role yang tidak berhak.

Aturan: branch `fase-2-pengajuan`, jalankan test sebelum commit, update docs/ROADMAP.md.
```

---

## Fase 3 — Penguatan Absensi Google Sheets

```text
Baca CLAUDE.md dan docs/ROADMAP.md dulu. Ini Fase 3.

Kondisi sekarang:
- app/Http/Controllers/SuperAdmin/PesertaMagangController.php membaca tab Google Sheets untuk bulan berjalan. Nama tab dibuat dengan strtoupper(now()->format('F Y')), yang menghasilkan nama bulan dalam BAHASA INGGRIS (misal "OCTOBER 2026"). Pastikan ini sesuai nama tab sebenarnya di sheet peserta.
- Layout sheet di-hardcode di konstanta controller (header baris 11, data mulai baris 13, kolom B–K).
- Credential service account di storage/app/google/service-account.json.
- View show.blade.php menghitung rekap (termasuk izin/sakit) langsung di Blade.

Tugas:
1. Tanyakan ke saya format nama tab bulan yang dipakai di Google Sheets (Inggris atau Indonesia), lalu sesuaikan.
2. Tambahkan pilihan bulan/tahun di halaman detail peserta (tidak hanya bulan berjalan).
3. Pindahkan konfigurasi layout sheet (baris header, kolom, path credential) ke config/services.php atau file config sendiri dengan nilai dari .env.
4. Pindahkan logika rekap (hadir, izin, sakit, dll.) dari Blade ke service/class tersendiri supaya bisa di-test.
5. Tambahkan cache singkat untuk hasil baca Google Sheets dan tombol "Muat ulang data".
6. Tulis test dengan GoogleSheetsService di-mock (tanpa akses internet): baca data, baris kosong dilewati, update absensi, error saat credential tidak ada.

Aturan: branch `fase-3-absensi`, jangan pernah commit file credential, jalankan test sebelum commit, update docs/ROADMAP.md.
```

---

## Fase 4 — Logbook Kegiatan & Penilaian Peserta

```text
Baca CLAUDE.md dan docs/ROADMAP.md dulu. Ini Fase 4.

Rencana awal proyek (lihat riwayat git, folder kerangka lama) menyebut modul: logbook, penilaian, laporan, mahasiswa.

Tugas:
1. Usulkan dulu rancangan tabel dan alur untuk:
   a. Logbook kegiatan harian peserta (tanggal, kegiatan, output, lampiran opsional, status diverifikasi pembimbing). Pertimbangkan bahwa kolom Activity/Output sudah ada di Google Sheets: apakah logbook cukup dibaca dari sheet, atau disimpan di database?
   b. Penilaian akhir peserta (aspek penilaian, nilai, catatan pembimbing, nilai akhir otomatis).
   Tunggu persetujuan saya sebelum membuat migration.
2. Buat migration, model + relasi ke PesertaMagang, FormRequest, controller, route (ikuti pola grup di routes/web.php), dan view dengan layout Fase 1.
3. Tampilkan ringkasan logbook dan nilai di halaman detail peserta.
4. Tulis feature test untuk CRUD dan hak akses tiap role.

Aturan: branch `fase-4-logbook-penilaian`, jalankan `php artisan migrate` dan test sebelum commit, update docs/ROADMAP.md.
```

---

## Fase 5 — Dashboard Statistik, Laporan & Export

```text
Baca CLAUDE.md dan docs/ROADMAP.md dulu. Ini Fase 5.

Kondisi sekarang: route admin.dashboard dan superadmin.dashboard hanya Route::view tanpa data.

Tugas:
1. Buat DashboardController untuk Admin dan SuperAdmin dengan data nyata: jumlah peserta aktif/selesai, pengajuan per status, peserta yang periodenya berakhir bulan ini, dan ringkasan absensi bila tersedia.
2. Tampilkan statistik memakai komponen kartu dari Fase 1 (grafik sederhana opsional, tanyakan dulu library yang ingin dipakai).
3. Buat halaman Laporan: rekap peserta per periode, rekap absensi per peserta, dan nilai akhir.
4. Tambahkan export Excel dan PDF (usulkan paket yang dipakai, misalnya maatwebsite/excel dan barryvdh/laravel-dompdf, dan pastikan kompatibel dengan Laravel 13 & PHP 8.4 sebelum menginstal).
5. Tulis test untuk perhitungan statistik dan endpoint export.

Aturan: branch `fase-5-laporan`, jalankan test sebelum commit, update docs/ROADMAP.md.
```

---

## Fase 6 — Keamanan, Otorisasi & Test

```text
Baca CLAUDE.md dan docs/ROADMAP.md dulu. Ini Fase 6.

Tugas:
1. Audit keamanan: rate limiting login, validasi upload banner, proteksi agar super admin tidak menghapus/menurunkan role dirinya sendiri, dan pastikan admin tidak bisa mengakses fitur khusus super admin.
2. Pertimbangkan Policy/Gate untuk model utama sebagai pelengkap RoleMiddleware. Jelaskan usulan sebelum menerapkan.
3. Tambahkan fitur ubah password/profil untuk pengguna yang sedang login.
4. Tambahkan log aktivitas sederhana (siapa mengubah apa, kapan) untuk aksi penting: ubah status pengajuan, ubah absensi, hapus data.
5. Lengkapi test: setiap route di routes/web.php punya minimal satu test akses (tamu, admin, super_admin). Laporkan coverage kasarnya.
6. Jalankan `vendor/bin/pint` (jika ada) untuk merapikan format kode.

Aturan: branch `fase-6-keamanan`, jalankan test sebelum commit, update docs/ROADMAP.md.
```

---

## Fase 7 — Persiapan Deploy

```text
Baca CLAUDE.md dan docs/ROADMAP.md dulu. Ini Fase 7 (terakhir).

Tugas:
1. Tanyakan ke saya target hosting (shared hosting/cPanel, VPS, atau lainnya) dan database produksi (MySQL atau tetap sqlite).
2. Siapkan .env.example yang lengkap (tanpa nilai rahasia) beserta penjelasan tiap variabel penting, termasuk konfigurasi Google Sheets.
3. Buat docs/DEPLOY.md: langkah instalasi di server, `composer install --no-dev`, `npm run build`, migrate, `storage:link`, cache config/route/view, penempatan credential Google, permission folder storage, dan cara update versi.
4. Cek bahwa APP_DEBUG=false tidak menampilkan error sensitif, dan halaman 403/404/500 punya tampilan berbahasa Indonesia.
5. Tambahkan seeder data awal untuk produksi (akun super admin dari .env, bukan password hardcode).
6. Opsional: GitHub Actions untuk menjalankan test setiap push (tanyakan dulu).

Aturan: branch `fase-7-deploy`, jalankan test sebelum commit, tandai semua fase selesai di docs/ROADMAP.md, lalu buat ringkasan akhir proyek.
```
