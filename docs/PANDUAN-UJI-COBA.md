# Panduan Uji Coba MAGENTA

Panduan singkat untuk mencoba semua fitur dari awal sampai akhir.

---

## 1. Menjalankan Aplikasi

Pilih **salah satu** cara.

**A. `artisan serve` (paling mudah, disarankan)**

1. Buka terminal di folder project.
2. Jalankan:
   ```
   C:\laragon\bin\php\php-8.4\php.exe artisan serve --port=8010
   ```
3. Buka **http://127.0.0.1:8010**.
4. Biarkan terminal tetap terbuka; tekan `Ctrl+C` untuk berhenti.

Server yang dijalankan Claude Code memakai cara yang sama, tetapi hanya hidup selama sesi
Claude berjalan.

**B. Laragon**

Database memakai SQLite, jadi MySQL tidak perlu dinyalakan. Langkahnya:

1. Laragon → menu **PHP → Version → php-8.4**. Project butuh PHP 8.4; PHP 8.3 tidak bisa.
2. **Start All**, lalu **Reload**.
3. Buka **http://web-magang-struktur.test**.

> Catatan: `magenta.test` di Laragon mengarah ke folder lain (`www/magenta`), bukan
> project ini.

**Tips:** pakai dua jendela, misalnya browser biasa + jendela *incognito*, supaya bisa login
sebagai **admin** dan **peserta** bersamaan.

---

## 2. Akun Uji

| Akun | Email | Password |
|---|---|---|
| Admin | `admin@magenta.local` | lihat `database/seeders/UserSeeder.php` baris 21 |
| Super Admin | `superadmin@magenta.local` | lihat `database/seeders/UserSeeder.php` baris 31 |
| Peserta | dibuat admin saat uji coba (langkah 4 di bawah) | password sementara muncul sekali di layar |

Halaman login: **/login**, untuk semua role. Bisa juga dibuka lewat tombol **Masuk Peserta**
di navbar website.

Halaman admin pengajuan sekarang berada di **/admin/pengajuan**. Dokumen pengajuan hanya
bisa dibuka admin dan super admin, atau pendaftarnya sendiri lewat tautan di Cek Status.

---

## 3. Siapa Bisa Apa

| Fitur | Super Admin | Admin | Peserta |
|---|:---:|:---:|:---:|
| Proses pengajuan magang (setujui/tolak, buat data peserta) | ✓ | ✓ | – |
| Data peserta, divisi & mentor per periode | ✓ | ✓ | – |
| Buat / reset / nonaktifkan **akun peserta** | ✓ | ✓ | – |
| Tinjau biodata (setujui / minta revisi) | ✓ | ✓ | – |
| Verifikasi logbook & izin, buka kunci tanggal, impor sheet lama | ✓ | ✓ | – |
| Isi penilaian akhir + PDF | ✓ | ✓ | – |
| Terbitkan surat keterangan | ✓ | ✓ | – |
| Monitoring, Feedback (rekap), Laporan & export | ✓ | ✓ | – |
| Hari libur | ✓ | ✓ | – |
| **Pengaturan** (tarif uang saku, batas isi logbook, periode, penanda tangan) | ✓ | lihat saja | – |
| Profil perusahaan, banner, konten website | ✓ | ✓ | – |
| **Kelola pengguna** (akun admin & super admin) | ✓ | – | – |
| **Aspek penilaian** (kelompok, bobot, predikat) | ✓ | – | – |
| **Log aktivitas** | ✓ | – | – |
| Isi biodata, logbook harian, izin/sakit, lihat Rekap Kehadiran | – | – | ✓ |
| Kirim feedback, unduh surat keterangan | – | – | ✓ |
| Ganti profil & password sendiri | ✓ | ✓ | ✓ |

Ringkasnya: **Super Admin = Admin + grup menu Sistem** (Kelola Pengguna, Aspek Penilaian,
Pengaturan, Log Aktivitas). Admin mengelola hari libur lewat menu **Hari Libur**.

Menu admin dikelompokkan: Utama, Kelola Peserta, Laporan, Konten Website, Sistem. Menu
Biodata, Logbook & Absensi, Penilaian, dan Surat Keterangan menampilkan daftar semua peserta
beserta statusnya; klik nama/aksi untuk membuka halaman per peserta.

---

## 4. Alur Mahasiswa Sampai Punya Akun

```
Mahasiswa daftar di website  →  Admin menyetujui pengajuan  →  Admin membuat data peserta
      →  Admin melengkapi periode magang  →  Admin membuat akun login (password sementara)
      →  Mahasiswa login & wajib ganti password  →  Mahasiswa memakai area peserta
```

Mahasiswa **tidak bisa mendaftar akun sendiri**. Akun selalu dibuat admin, karena hanya
peserta yang sudah diterima yang boleh masuk.

---

## 5. Skenario Uji (urut, ±30 menit)

Centang setiap langkah yang berhasil.

### A. Persiapan (login **Super Admin**)
- [ ] **Profil Perusahaan**: isi nama perusahaan, alamat, dan kota (dipakai di kop surat & PDF).
- [ ] **Pengaturan Magang**:
  - [ ] isi kota surat dan nama penanda tangan "Mengetahui" & "Menyetujui";
  - [ ] cek tarif (Rp50.000 / Rp25.000);
  - [ ] tambah satu hari libur.
- [ ] **Divisi**: tambah 2 divisi, misalnya *Unit Umum* dan *Konsultan*.
- [ ] **Aspek Penilaian**: lihat kelompok Hard Skill 60% / Soft Skill 40% dan tabel predikat.

### B. Pendaftaran (tanpa login)
- [ ] Buka halaman utama → bagian **Daftar**, lalu isi form sampai muncul nomor **MG-00000x**.
- [ ] Bagian **Cek Status**: masukkan **kode pengajuan** (MG-00000x) + email. Status tampil
  "Menunggu", lengkap dengan tautan **Lihat surat pengantar** yang berlaku 30 menit.
- [ ] (Keamanan) Salin tautan itu, ganti angka ID di URL, lalu buka: hasilnya **403**.

### C. Admin memproses (login **Admin**)
- [ ] **Pengajuan Magang** → buka pengajuan tadi → ubah status **Disetujui**.
- [ ] Klik **Buat Data Peserta**, lalu di form edit isi **tanggal mulai & selesai magang**.
  - Agar fitur Feedback & Surat bisa dicoba hari ini, isi **tanggal selesai ±2 minggu dari hari ini**.
- [ ] Detail peserta → kartu **Divisi & Mentor per Periode**: pilih divisi + nama mentor → Simpan.
- [ ] Kartu **Akun Login Peserta** → **Buat Akun Login**, lalu **catat password sementara**
  (hanya tampil sekali).

### D. Peserta mulai (login **Peserta** di jendela lain)
- [ ] Login dengan email + password sementara: langsung diminta **ganti password**.
- [ ] **Biodata**:
  - [ ] isi sebagian → **Simpan Draft** (progres % naik);
  - [ ] lengkapi semua, centang pernyataan → **Kirim untuk Ditinjau**.
- [ ] **Logbook Harian**:
  - [ ] isi **hari ini**: jam pagi masuk 08:00 + siang keluar 16:30 (hari penuh), Activity, lokasi, upload 1 foto;
  - [ ] isi kemarin dengan satu sesi saja, pagi masuk–keluar (setengah hari);
  - [ ] pastikan tanggal besok **terkunci 🔒**.
- [ ] **Ajukan Izin/Sakit**: pilih *Sakit* untuk 2 hari ke depan + upload surat → tanggal-tanggalnya terisi "Sakit".
- [ ] Lihat **Rekap Periode** di bawah tabel, lalu klik **Unduh PDF**.

### E. Admin memverifikasi (kembali ke **Admin**)
- [ ] **Monitoring**: peserta tampil dengan progres biodata dan "Hari ini ✓".
- [ ] Detail peserta → **Biodata Peserta**:
  - [ ] coba **Minta revisi** + catatan;
  - [ ] peserta memperbaiki & mengirim ulang;
  - [ ] lalu **Setujui**.
- [ ] Detail peserta → **Logbook Harian & Izin**:
  - [ ] centang hari → tandai **Diterima**;
  - [ ] coba **Ditolak** dengan catatan;
  - [ ] **Terima** izin sakit;
  - [ ] coba **Buka kunci** satu tanggal lama.
- [ ] Cek di peserta: hari yang ditolak bisa diperbaiki; hari yang diterima terkunci.
- [ ] **Penilaian Akhir**: isi 15 aspek (nilai akhir & predikat berubah langsung) + kesimpulan
  → Simpan → **Lembar Penilaian (PDF)**.

### F. Akhir magang
- [ ] Peserta: menu **Feedback** terbuka (karena tanggal selesai < 1 bulan lagi) → isi → **Kirim**.
- [ ] Admin: menu **Feedback Magang**: rata-rata & jawaban tampil.
- [ ] Admin: edit data peserta → ubah **status = Selesai** → otomatis ada **draft surat**.
- [ ] Detail peserta → **Surat Keterangan Magang**:
  - [ ] semua syarat ✓ → **Pratinjau PDF**;
  - [ ] **Terbitkan Surat** → nomor `001/SKM/…`.
- [ ] Peserta: menu **Surat Keterangan** → **Unduh Surat (PDF)**. Menu **Feedback** sekarang terkunci.
- [ ] Peserta selesai tidak bisa lagi mengubah logbook/biodata (hanya melihat).

### G. Laporan & fitur Super Admin
- [ ] **Laporan** → tab *Kehadiran & Uang Saku* (pilih periode), *Nilai Akhir*, *Feedback*
  → coba **Excel / PDF / CSV**.
- [ ] (Super Admin) **Log Aktivitas**: semua tindakan di atas tercatat.
- [ ] (Super Admin) **Kelola Pengguna**: tambah akun admin baru (akun peserta tidak tampil di sini).
- [ ] (Opsional) Logbook Harian → **Impor Riwayat**: upload buku kerja `.xlsx` lama untuk peserta
  yang periode magangnya mencakup tanggal di buku kerja.

---

## 6. Hal yang Sering Membingungkan

| Pertanyaan | Jawaban |
|---|---|
| Menu Feedback masih 🔒 | Terbuka mulai 1 bulan sebelum tanggal selesai magang. Majukan tanggal selesai di data peserta. |
| Tombol Terbitkan Surat tidak muncul | Butuh: status Selesai, biodata **Disetujui**, penilaian diisi, feedback dikirim. |
| Tanggal logbook lama tidak bisa diisi | Melewati batas isi mundur (Pengaturan Magang, bawaan 7 hari). Admin bisa **Buka kunci**. |
| Peserta tidak bisa login | Akun dinonaktifkan, atau password salah. Admin bisa **Reset Password**. |
| Periode "Agustus" mulai 28 Juli | Benar: periode berjalan tanggal 28 s.d. 27 bulan berikutnya. |
| Uang saku 0 padahal hadir | Jam belum lengkap (butuh pagi masuk + siang keluar, atau satu sesi lengkap), atau hari itu Ditolak. |

> Data uji yang dibuat masuk ke database lokal (`database/database.sqlite`). Untuk mulai
> bersih, salin dulu file tersebut sebagai cadangan sebelum mencoba.
