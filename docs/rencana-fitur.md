# Rencana Fitur: Memindahkan Buku Kerja Magang ke Website

Status: tahap 1–11 (Fase 8–18) **sudah selesai** dan sudah di-merge ke `main`.
**Revisi 4** ([bagian 8](#8-revisi-4-keamanan-dan-fitur-tambahan)) sudah disetujui
dengan jawaban 1A, 2A, 3A, 4B, 5A. Pengerjaan dimulai dari Fase 0 (`fase-0-keamanan`).

Acuan: buku kerja `docs/reverensi/absensi-contoh.xlsx.xlsx`. Tujuannya, semua isi buku
kerja itu bisa diisi dan dipantau langsung di website.

| Sheet di Excel | Menjadi fitur |
|---|---|
| PROFIL MAHASISWA | Biodata peserta (bagian 3B) |
| JUNI 2026 … NOVEMBER 2026 | Logbook harian, kehadiran, rekap periode, dan uang saku (bagian 3C–3D) |
| *(Divisi di kepala sheet bulanan)* | Penempatan divisi per periode (bagian 3E) |
| PENILAIAN MHS MAGANG | Penilaian akhir yang sudah ada, perlu disesuaikan (bagian 3H) |
| FEEDBACK MHS MAGANG | Feedback magang (bagian 3F) |
| SKET. MAGANG | Surat keterangan magang (bagian 3G) |

---

## 1. Keputusan yang Sudah Diambil

| No | Topik | Keputusan |
|---|---|---|
| 1 | Sumber logbook | **A**: database aplikasi; sheet lama hanya untuk riwayat (lihat pertanyaan 3) |
| 2 | Biodata | **A**: field tetap, mengikuti sheet PROFIL MAHASISWA (termasuk NIK), tanpa kontak darurat |
| 3 | Akun peserta | **A**: dibuat admin, password sementara wajib diganti saat login pertama |
| 4 | Cakupan admin | **A**: semua admin melihat semua peserta |
| 5 | Jam kerja | **A**: 4 kolom (pagi masuk/keluar, siang masuk/keluar), semuanya tetap ada |

Ketentuan tambahan dari Anda:

- **Logbook:**
  - status kehadiran Hadir/Izin/Sakit/Libur;
  - batas isi mundur diatur admin;
  - hari ini boleh diisi (termasuk Sabtu/Minggu), tanggal yang belum tiba terlihat tapi terkunci;
  - izin/sakit beberapa hari memakai satu bukti untuk satu rentang tanggal.
- **Periode:** tanggal 28 s.d. 27 bulan berikutnya.
- **Uang saku:**
  - tarif diatur admin dan bisa berubah;
  - aturan hari penuh/setengah hari dipertahankan;
  - rekap menampilkan total hadir penuh, setengah hari, izin/sakit, dan uang saku.
- **Divisi:** dicatat per periode (rotasi).
- **Fitur baru:** feedback magang dan surat keterangan magang.
- **Penilaian:** dibandingkan dengan Excel saja, belum diubah.

---

## 2. Temuan dari Buku Kerja

Hal-hal berikut memengaruhi rancangan:

1. **Periode 28–27 terbukti di data.**
   - Sheet JULI berisi 28 Juni s.d. 27 Juli, AGUSTUS berisi 28 Juli s.d. 27 Agustus, dan
     SEPTEMBER berisi 28 Agustus s.d. 27 September.
   - Nama periode mengikuti **bulan tanggal akhirnya**.
   - Periode pertama dimulai dari tanggal mulai magang (sheet JUNI dimulai awal Juni s.d.
     27 Juni). Periode terakhir ditutup pada tanggal selesai magang.
2. **Ada dua format sheet bulanan.**
   - Format lama (JUNI–AGUSTUS):
     - header baris 8, data baris 10–40;
     - kolom: No, Tanggal, Pagi, Siang, Keterangan, Paraf (`v`).
   - Format baru (SEPTEMBER dst.):
     - header baris 11, data baris 13–43;
     - kolom: Activity, Output, Lokasi, Action.
   - `AbsensiSheetService` sekarang hanya memahami format baru, jadi riwayat Juni–Agustus
     tidak akan terbaca (lihat pertanyaan 3).
3. **Isian jam di Excel tidak seragam.** Contohnya `08.00`, `07:56`, pecahan jam Excel
   (`0.3388…`), `8;10`, `??`, dan angka tanggal yang salah format. Form website memakai
   input jam baku, jadi masalah ini selesai dengan sendirinya. Impor riwayat perlu
   pembersihan.
4. **Cara Excel mengisi jam berbeda dari aturan di kode** (lihat pertanyaan 1).
   - Hampir semua hari penuh di Excel hanya mengisi **pagi masuk** dan **siang keluar**
     (contoh 08.00 dan 16.30); kolom pagi keluar dan siang masuk kosong.
   - Excel tetap menghitungnya sebagai hari penuh: September tercatat 20 hari × Rp50.000 =
     Rp1.000.000.
   - Aturan `RekapAbsensi` sekarang mensyaratkan **keempat kolom** terisi untuk hari penuh,
     sehingga baris seperti itu akan dianggap *tidak lengkap*.
5. **Status kehadiran di Excel masih teks bebas di kolom keterangan.** Contohnya "Sabtu",
   "Hari Minggu", "Libur Tahun Baru Islam", "Hari Libur Nasional HUT kemerdekaan RI",
   "Cuti Bersama Maulid Nabi", "Tidak hadir Dikarenakan Sakit", dan "izin tidak masuk…".
   Di website, status ini menjadi pilihan tetap.
6. **Footer sheet bulanan berisi:**
   - total masuk penuh, total setengah hari, total absen/izin/sakit;
   - tarif penuh, tarif setengah hari, total uang saku;
   - nomor rekening, nama bank, atas nama;
   - blok tanda tangan: Peserta, Mengetahui (Pembimbing/Mentor + jabatan), Mengetahui
     (Staff GA & HRD), Menyetujui (Manager GA & FA).
7. **Mentor ikut berganti saat rotasi divisi.** Sheet September mencantumkan mentor Divisi
   Konsultan sebagai penanda tangan "Mengetahui". Karena itu mentor dicatat per periode
   bersama divisi.
8. **"NIM" di kepala sheet lama sebenarnya nomor peserta.** Formatnya `MG` + tahun + bulan
   + nomor urut, misalnya `MG202606…`, sedangkan NIM kampus ada di biodata. Website
   menyimpan keduanya.
9. **Sheet FEEDBACK berisi lebih dari 10 skala + 4 terbuka.** Selain itu ada:
   - satu pertanyaan pilihan: *bersedia merekomendasikan?* (Sangat Bersedia / Bersedia /
     Ragu-ragu / Tidak Bersedia);
   - satu pertanyaan *evaluasi keseluruhan* (teks);
   - pernyataan kejujuran.

   Rancangan ini memasukkan semuanya.
10. **Sheet SKET. MAGANG baru berisi judul.** Isi surat diusulkan di bagian 3G.

> ⚠️ **Data pribadi asli:** file contoh berisi NIK, nomor rekening, nomor HP, dan alamat
> asli, dan saat ini *untracked* di git. Sebaiknya jangan di-commit. Pilihannya:
> `docs/reverensi/` dimasukkan ke `.gitignore`, atau isi file diganti data samaran.

---

## 3. Rancangan per Fitur

### A. Aktor dan akun peserta (tidak berubah dari revisi 1)

- **Akun disimpan di `users`** dengan `role = 'peserta'`, lalu dihubungkan lewat
  `peserta_magangs.user_id`. Login, rate limit, ganti password, dan profil dipakai ulang.
- **Route lama memakai `role:admin,super_admin`,** sehingga peserta otomatis ditolak.
  `AksesRouteTest` ditambah kolom peserta untuk membuktikannya.
- **Admin mengelola akun dari halaman peserta:** buat akun (password sementara ditampilkan
  sekali), reset password, nonaktifkan, dan hapus. Semua aksi dicatat di `LogAktivitas`.
- **Login pertama wajib ganti password,** dijaga middleware `WajibGantiPassword`.
- **Peserta berstatus `selesai` hanya bisa membaca.** Pengecualiannya feedback (bagian 3F).
  Aturan ini dijaga middleware `PesertaAktif`.
- **Data milik peserta dijaga Policy.** Peserta A tidak bisa membuka biodata, logbook, atau
  bukti milik peserta B.
- **Nomor peserta:** kolom baru `peserta_magangs.nomor_peserta`, dibuat otomatis dengan
  format `MG{tahun}{bulan}{urut 3 digit}` mengikuti Excel.

### B. Biodata (mengikuti sheet PROFIL MAHASISWA)

Tabel `biodata_pesertas`, satu baris per peserta:

| Kelompok | Field |
|---|---|
| Data pribadi | nama lengkap, nama panggilan, jenis kelamin, tempat lahir, tanggal lahir, **NIK**, alamat KTP, alamat domisili, tautan Maps domisili, no WhatsApp/HP, email, tautan LinkedIn/portofolio, tautan Instagram |
| Data akademik | perguruan tinggi, fakultas/jurusan, NIM, semester, IPK, nama dosen pembimbing, kontak dosen pembimbing, email dosen pembimbing |
| Detail program | posisi/divisi yang dilamar, jenis program (Mandiri / Wajib dari Kampus / MBKM / Lainnya + keterangan), durasi (3 bulan / 6 bulan / Lainnya + jumlah bulan), periode magang, mentor magang |
| Keterampilan dan kesehatan | **keterampilan** (daftar, bisa banyak baris), **bahasa yang dikuasai** (daftar), **riwayat penyakit/alergi** (daftar) |
| Rekening uang saku | nomor rekening, nama bank, atas nama |
| Pernyataan | centang "data benar", tempat dan tanggal pernyataan |

Pembagian siapa mengisi apa:

- **Admin menyiapkan:** periode magang, jenis program, durasi, dan posisi/divisi awal.
  Bagian ini terkunci untuk peserta. Data yang sudah ada disalin otomatis dari
  `mahasiswas` / `peserta_magangs`.
- **Peserta melengkapi** semua field lainnya, lalu mengirim dengan mencentang pernyataan.
- **Alur status:** `draft` → `diajukan` → `disetujui` / `perlu_revisi` (dengan catatan
  admin). Setelah `disetujui`, biodata terkunci. Nama, NIM, kampus, dan prodi yang
  disetujui disalin ke `peserta_magangs` supaya laporan lama tetap benar.
- **Daftar keterampilan, bahasa, dan penyakit** disimpan sebagai kolom JSON (cast `array`).
  Di form, tiap daftar tampil sebagai baris yang bisa ditambah atau dihapus.
- **Data sensitif:**
  - NIK, nomor rekening, dan riwayat penyakit disimpan terenkripsi (cast `encrypted`);
  - NIK disamarkan di daftar (`3518********0002`);
  - hanya admin dan peserta pemilik yang bisa melihat isi lengkap;
  - konsekuensinya, `APP_KEY` di server tidak boleh diganti setelah ada data (dicatat di
    DEPLOY nanti).
- **Progres biodata** = jumlah field wajib yang terisi ÷ total field wajib, ditampilkan di
  monitoring.
- **Mentor dan dosen pembimbing dipisah.** Kolom lama `peserta_magangs.pembimbing` (teks
  bebas) tidak jelas maksudnya. Mentor kantor dicatat di penempatan per periode (3E),
  sedangkan dosen pembimbing di biodata.

### C. Logbook harian dan kehadiran

**Tampilan seperti sheet bulanan.** Peserta melihat satu tabel per periode (28–27), dan
semua tanggal dalam periode itu tampil sebagai baris. Tiap baris punya salah satu dari tiga
keadaan:

- **bisa diisi:** hari ini, atau tanggal lampau yang masih dalam batas mundur;
- **terkunci (lewat batas):** tanggal lampau yang melewati batas mundur, kecuali dibuka
  admin;
- **terkunci (belum tiba):** tanggal di masa depan, terlihat tapi tidak bisa diisi.

Tabel `logbook_harians`, unik per (peserta, tanggal):

| Kolom | Isi |
|---|---|
| `kehadiran` | `hadir` / `izin` / `sakit` / `libur` |
| `pagi_masuk`, `pagi_keluar`, `siang_masuk`, `siang_keluar` | `time`, nullable; wajib sesuai aturan hari penuh/setengah hari (pertanyaan 1) bila `hadir` |
| `activity`, `output`, `lokasi` | teks; activity wajib bila `hadir`; lokasi memberi saran dari isian sebelumnya ("Kantor Jogja", dsb.) |
| `status_verifikasi` | `menunggu` (bawaan) / `diterima` / `ditolak` / `review`, sama dengan `config('absensi.action_options')` |
| `catatan_verifikasi`, `diverifikasi_oleh`, `diverifikasi_pada` | diisi admin |
| `izin_peserta_id` | terisi bila hari ini bagian dari pengajuan izin/sakit rentang |
| `sumber` | `aplikasi` / `impor` (riwayat dari sheet) |

Ketentuan isian:

- **Bukti per hari (opsional):** tabel `logbook_buktis` berisi path, nama asli, mime, dan
  ukuran.
  - Maksimal 3 file per hari, berformat jpg/png/pdf, masing-masing paling besar 2 MB.
  - File disimpan di disk `local` (privat) dan diunduh lewat route yang dicek Policy.
- **Izin/sakit beberapa hari:** tabel `izin_pesertas` berisi jenis (`izin`/`sakit`),
  tanggal mulai, tanggal selesai, keterangan, file bukti, dan status verifikasi.
  - Peserta mengunggah satu bukti dan memilih rentang tanggal sesuai surat.
  - Sistem membuat atau memperbarui baris `logbook_harians` untuk **setiap tanggal** dalam
    rentang (`kehadiran = izin/sakit`, `izin_peserta_id` terisi).
  - Tanggal yang sudah `diterima` sebagai hadir tidak ditimpa, dan peserta diberi
    peringatan.
  - Admin cukup memverifikasi izinnya sekali, lalu statusnya diteruskan ke semua hari dalam
    rentang.
- **Libur:**
  - Admin mengelola daftar hari libur nasional dan cuti bersama di tabel `hari_liburs`.
  - Sabtu, Minggu, dan tanggal di daftar itu tampil otomatis sebagai "Libur" bila peserta
    tidak mengisi apa pun.
  - Peserta tetap boleh mengisi `hadir` di hari itu jika masuk kerja.
- **Batas isi mundur:** diatur admin di halaman Pengaturan Magang (bawaan 7 hari), bukan
  dari `.env`.
  - Admin bisa membuka kunci satu tanggal untuk peserta tertentu, atau mengisi/mengoreksi
    langsung. Semuanya tercatat di log.
- **Hak ubah:** entri bisa diubah peserta selama statusnya `menunggu` / `ditolak` /
  `review`, dan terkunci setelah `diterima`.
- **Rentang tanggal:** isian hanya boleh di dalam periode magang (`periode_mulai` s.d.
  `periode_selesai`).

### D. Periode 28–27, rekap, dan uang saku

- **Service baru `PeriodeMagang`** menentukan periode suatu tanggal.
  - Tanggal ≥ 28 masuk periode bulan berikutnya; tanggal < 28 masuk periode bulan itu.
  - Contoh: 28 Juli – 27 Agustus = **Periode Agustus 2026**, dengan kunci `2026-08`.
  - Periode pertama dipotong di tanggal mulai magang, dan periode terakhir di tanggal
    selesai.
  - Tanggal awal periode (28) disimpan di Pengaturan Magang supaya bisa diubah tanpa coding.
- **Tarif uang saku tidak di-hardcode.** Tabel `tarif_uang_sakus` berisi `berlaku_mulai`,
  `tarif_hari_penuh`, `tarif_setengah_hari`, dan `dibuat_oleh`.
  - Tiap hari dihitung dengan tarif yang berlaku pada tanggal itu, sehingga perubahan tarif
    tidak mengubah rekap periode yang sudah lewat.
  - Nilai `.env` (`UANG_SAKU_*`) hanya dipakai sekali sebagai tarif awal saat migration.
- **Aturan hari penuh/setengah hari** tetap memakai `RekapAbsensi::jenisKehadiran()`. Baris
  DB diubah ke bentuk array yang sama, lalu dihitung oleh fungsi yang sama. Aturan jam
  kerjanya mengikuti keputusan 1 di bagian 7.
- **Rekap per periode** (untuk peserta dan admin):
  - total hadir penuh, total setengah hari, total izin/sakit, total libur, hari belum diisi;
  - tarif yang dipakai dan total uang saku;
  - rekening peserta dari biodata.
- **Cetak / PDF rekap periode** mengikuti format sheet bulanan:
  - kepala: nama, divisi periode itu, nomor peserta;
  - tabel harian;
  - footer rekap dan rekening;
  - blok tanda tangan: Peserta, Mentor periode itu, serta Staff HRD dan Manager dari
    Pengaturan Magang.

  Format ini memakai dompdf yang sudah terpasang.

### E. Divisi dan rotasi per periode

- Tabel `divisis` (nama, aktif) dikelola admin. Contoh isinya: Unit Umum, Divisi Bisnis,
  Marketing, Konsultan.
- Tabel `penempatan_pesertas`: `peserta_magang_id`, `periode` (kunci `Y-m`), `divisi_id`,
  `mentor_nama`, `mentor_jabatan`, unik per (peserta, periode).
  - Admin menentukan penempatan dari halaman detail peserta.
  - Periode baru otomatis memakai penempatan periode sebelumnya sampai admin mengubahnya.
- Divisi dan mentor periode ditampilkan di logbook, rekap/cetak periode, monitoring, dan
  surat keterangan (riwayat rotasi).

### F. Feedback magang (sheet FEEDBACK MHS MAGANG)

- **Isi:**
  - 10 pertanyaan skala 1–5 (5 = Sangat Setuju … 1 = Sangat Tidak Setuju);
  - 4 pertanyaan terbuka (paling disukai, kendala, yang perlu diperbaiki, saran);
  - pertanyaan *bersedia merekomendasikan* (4 pilihan);
  - *evaluasi keseluruhan* (teks);
  - centang pernyataan.
- **Penyimpanan:**
  - Teks pertanyaan disimpan di `config/feedback.php`, jadi tidak di-hardcode di view.
  - Tabel `feedback_pesertas`, satu baris per peserta: `skor` (JSON 10 angka), 4 kolom teks
    terbuka, `rekomendasi`, `evaluasi_keseluruhan`, `pernyataan`, `dikirim_pada`.
- **Menu di area peserta:**
  - Muncul **setelah menu Logbook**.
  - Sebelum H-30 dari `periode_selesai`, menu tampil terkunci dengan keterangan "Dibuka
    mulai {tanggal}".
  - Dari H-30, peserta bisa mengisi, mengedit, dan mengirim ulang.
  - Feedback tetap bisa diedit setelah status `selesai`, sampai surat keterangan
    diterbitkan.
- **Admin:**
  - melihat feedback per peserta;
  - rekap: rata-rata per pertanyaan, rata-rata total, sebaran jawaban rekomendasi, daftar
    jawaban terbuka;
  - filter periode/angkatan;
  - export CSV/XLSX lewat pola `LaporanMagang` yang sudah ada.

### G. Surat keterangan magang (sheet SKET. MAGANG)

**Usulan waktu pembuatan:** saat status peserta diubah menjadi `selesai`, sistem membuat
**draft** surat secara otomatis. Admin memeriksa draft itu lalu menekan **Terbitkan**.
Sebelum terbit, sistem memeriksa syarat (lihat pertanyaan 5). Setelah terbit:

- surat bisa diunduh peserta dari area peserta;
- feedback terkunci;
- surat yang diterbitkan ulang (karena koreksi) tetap memakai nomor yang sama, dan tercatat
  di log.

Tabel `surat_keterangans`: `peserta_magang_id` (unik), `nomor_surat` (unik), `tanggal_surat`,
`status` (`draft`/`terbit`), `data` (JSON snapshot), `diterbitkan_oleh`, `diterbitkan_pada`.

| Isi surat | Diambil dari |
|---|---|
| Kop, nama dan alamat perusahaan, logo | `perusahaans` (Profil Perusahaan) |
| Nomor surat | Pengaturan Magang: format, misalnya `{urut}/SKM/{kode}/{bulan romawi}/{tahun}`, dengan nomor urut per tahun |
| Nama, NIM, perguruan tinggi, fakultas/jurusan | biodata yang disetujui |
| Periode magang (tanggal mulai–selesai), nomor peserta | `peserta_magangs` |
| Divisi yang pernah ditempati | `penempatan_pesertas` (riwayat rotasi) |
| Nilai akhir dan predikat (opsional, bisa disembunyikan) | penilaian akhir |
| Kota dan tanggal surat, nama dan jabatan penanda tangan | Pengaturan Magang |

PDF dibuat dari snapshot `data`. Tujuannya, surat yang sudah terbit tidak berubah walaupun
data peserta diedit kemudian.

### H. Penilaian: perbandingan dengan sheet PENILAIAN MHS MAGANG (belum diubah)

| Hal | Kode saat ini | Excel | Dampak |
|---|---|---|---|
| Struktur aspek | 6 aspek datar (Kedisiplinan, Tanggung Jawab, Kemampuan Teknis, Kerja Sama, Inisiatif, Komunikasi) | 2 kelompok: **Hard Skill 7 aspek**, **Soft Skill 8 aspek** | Perlu kolom kelompok di `aspek_penilaians` |
| Bobot | Bobot per aspek (bilangan bulat, total 100) | Bobot per **kelompok** (60% / 40%); aspek dalam kelompok dirata-rata sama rata | 60/7 = 8,57 tidak bisa disimpan sebagai bobot bulat per aspek; perlu bobot kelompok |
| Rumus | Σ(nilai × bobot) ÷ Σbobot | (rata-rata Hard × 60%) + (rata-rata Soft × 40%) | Rumus `hitungNilaiAkhir` berubah |
| Skala | 0–100 | 1–100 | Batas minimum validasi berubah (kecil) |
| Predikat | Huruf A–E (≥85, ≥75, ≥65, ≥50) dalam kolom `char(1)` | 5 predikat teks dengan keterangan: 0–20 Perlu Pembinaan, 20,1–40 Cukup, 40,1–60 Baik, 60,1–80 Memuaskan, 80,1–100 Sangat Memuaskan | Kolom predikat harus diubah ke string, dan perlu tabel predikat |
| Kesimpulan | Satu kolom `catatan_pembimbing` | **3 isian:** kekuatan dan keunggulan, aspek utama yang perlu diperbaiki, langkah perbaikan; ditambah kalimat hasil penilaian | Perlu 3 kolom teks (Anda menyebut 2, Excel berisi 3) |
| Kepala dan tanda tangan | Tidak ada cetak penilaian | Data peserta, program pendidikan, periode; tanda tangan Penilai (mentor), Mengetahui, Menyetujui | Perlu cetak/PDF penilaian |

Data penilaian lama tetap aman: `penilaian_details` sudah menyimpan salinan nama dan bobot
aspek. Cara penyesuaiannya menunggu pertanyaan 4.

### I. Dashboard monitoring admin

- **Semua dari DB,** tanpa memanggil Google API.
- **Kartu statistik:**
  - akun peserta aktif;
  - biodata lengkap / perlu revisi / belum diisi;
  - logbook hari ini terisi vs belum;
  - entri dan izin menunggu verifikasi;
  - feedback terkumpul;
  - surat siap terbit.
- **Tabel per peserta:**
  - divisi periode ini;
  - progres biodata (%);
  - jumlah hari terisi di periode berjalan, hadir penuh, setengah hari, izin/sakit;
  - entri terakhir;
  - jumlah menunggu verifikasi;
  - uang saku sementara.

  Tabel bisa difilter (status, divisi, "belum isi hari ini") dan diurutkan.
- **Riwayat logbook terbaru** lintas peserta, dengan tautan ke halaman verifikasi.

---

## 4. Database

| Tabel | Baru/Ubah | Isi pokok |
|---|---|---|
| `users` | Ubah | `role` enum → `string(20)` (nilai baru `peserta`), `wajib_ganti_password`, `aktif` |
| `peserta_magangs` | Ubah | `user_id` (FK nullable unique), `nomor_peserta` (unique) |
| `biodata_pesertas` | Baru | field PROFIL MAHASISWA + rekening, status, catatan revisi |
| `divisis` | Baru | nama, aktif |
| `penempatan_pesertas` | Baru | peserta, periode `Y-m`, divisi, mentor nama/jabatan |
| `logbook_harians` | Baru | lihat 3C |
| `logbook_buktis` | Baru | file bukti per hari |
| `izin_pesertas` | Baru | izin/sakit rentang + bukti + verifikasi |
| `hari_liburs` | Baru | tanggal (unique), keterangan |
| `tarif_uang_sakus` | Baru | berlaku_mulai, tarif penuh, tarif setengah hari |
| `pengaturan_magangs` | Baru (1 baris) | batas isi mundur (hari), tanggal awal periode (28), kota surat, nama/jabatan Staff HRD dan Manager, format nomor surat |
| `feedback_pesertas` | Baru | lihat 3F |
| `surat_keterangans` | Baru | lihat 3G |
| `aspek_penilaians`, `penilaian_pesertas` (+ `kelompok_penilaians`, `predikat_penilaians`) | Ubah/Baru | hanya jika pertanyaan 4 dijawab A atau B |
| `absensis` | Tidak disentuh | tabel lama yang tidak dipakai |

---

## 5. Halaman, Route, dan Middleware

**Middleware:**

- `role:peserta` memakai `RoleMiddleware` yang sudah ada, tanpa perubahan.
- Baru: `WajibGantiPassword` dan `PesertaAktif`.
- `AuthController::login` ditambah redirect peserta, dan menolak akun dengan `aktif = false`.

**Area peserta** (`/peserta`, nama route `peserta.*`; sidebar dengan urutan berikut):

1. Dashboard: progres biodata, ringkasan periode berjalan, catatan revisi/penolakan.
2. Biodata: form bertahap per kelompok data.
3. Logbook: tabel periode 28–27, isi/ubah per hari, upload bukti, ajukan izin/sakit rentang.
4. Rekap dan Uang Saku: per periode, dengan cetak/PDF.
5. Feedback: terkunci sampai H-30.
6. Surat Keterangan: unduh setelah terbit.
7. Profil dan ganti password (`profil.*` diperluas untuk peserta).

**Area admin** (route baru di grup `superadmin.*` untuk admin + super admin; nama route lama
tidak diubah):

- **Detail peserta:**
  - kartu akun (buat/reset/nonaktifkan);
  - penempatan divisi per periode;
  - tab Biodata (tinjau, setujui, minta revisi);
  - tab Logbook (verifikasi harian dan massal, verifikasi izin, buka kunci tanggal);
  - tab Rekap periode;
  - tab Feedback;
  - tab Surat.
- **Halaman `superadmin.peserta-magang.logbook`** dipakai ulang dengan sumber DB.
- **Pengaturan Magang:** batas mundur, tanggal awal periode, riwayat tarif uang saku, hari
  libur, penanda tangan, format nomor surat.
- **Master Divisi.**
- **Rekap Feedback.**
- **Dashboard monitoring:** memperluas `dashboard/statistik` dan `StatistikMagang`, atau
  service baru `MonitoringPeserta`.
- **Laporan:** filter "bulan" diganti "periode 28–27", dan sumbernya dari DB.

**Test:**

- `AksesRouteTest` diberi role ke-4 dan semua route baru;
- test Policy untuk akses lintas peserta;
- test `PeriodeMagang` (batas 27/28, Februari, potongan awal/akhir magang);
- test tarif per tanggal;
- test izin rentang;
- test jendela H-30 feedback;
- test penomoran surat.

---

## 6. Urutan Pengerjaan

Setiap tahap dikerjakan di branch sendiri, diuji, lalu di-merge setelah Anda setujui.

| Tahap | Branch | Isi | Bergantung pada |
|---|---|---|---|
| 1 | `fase-8-aktor-peserta` | Role peserta, `user_id`, `nomor_peserta`, login/redirect, middleware, `AksesRouteTest` 4 role | – |
| 2 | `fase-9-akun-peserta` | Kelola akun oleh admin, ganti password wajib, dashboard peserta kosong | 1 |
| 3 | `fase-10-pengaturan-magang` | Pengaturan Magang, tarif berjangka, hari libur, `PeriodeMagang`, master divisi dan penempatan per periode | 1 |
| 4 | `fase-11-biodata` | Biodata, alur setuju/revisi, enkripsi data sensitif, progres | 2 |
| 5 | `fase-12-logbook` | Logbook harian, kehadiran, bukti, izin rentang, verifikasi admin, rekap periode dan uang saku, cetak periode | 3, 4 (rekening) |
| 6 | `fase-13-impor-riwayat` | Impor riwayat sheet lama (jika pertanyaan 3 = A) | 5 |
| 7 | `fase-14-monitoring` | Dashboard monitoring | 4, 5 |
| 8 | `fase-15-feedback` | Feedback peserta dan rekap admin | 2 |
| 9 | `fase-16-penilaian` | Penyesuaian penilaian (sesuai pertanyaan 4) dan cetak penilaian | – |
| 10 | `fase-17-surat-keterangan` | Draft otomatis, terbitkan, PDF, nomor surat | 3, 4, 8, 9 |
| 11 | `fase-18-laporan` | Laporan/export pakai periode dan DB, update ROADMAP dan README-STRUKTUR | semua |

Fase 7 (deploy) dikerjakan setelah tahap 11. Fitur ini menambah kebutuhan server:
penyimpanan file privat, `upload_max_filesize`, backup `storage/app`, dan `APP_KEY` yang
tidak boleh berganti.

---

## 7. Keputusan Revisi 2

| No | Topik | Keputusan |
|---|---|---|
| 1 | Aturan jam kerja | Lihat penjelasan di bawah tabel |
| 2 | Hari yang dihitung uang saku | **A**: semua hari `hadir` yang tidak `ditolak` |
| 3 | Riwayat sheet lama | **A**: impor sekali ke database, lalu Google Sheets tidak dibaca lagi. **Format baku logbook = format sheet September** |
| 4 | Penilaian | **A**: struktur Excel (Hard 60% / Soft 40%, predikat teks, 3 isian kesimpulan), bisa diatur Super Admin |
| 5 | Syarat surat keterangan | **A**: draft otomatis saat `selesai`, admin menekan Terbitkan; syarat biodata disetujui, penilaian diisi, feedback dikirim |

### Aturan jam kerja (keputusan 1)

Form tetap memiliki 4 kolom jam:

| Kolom | Hari penuh |
|---|---|
| Pagi masuk | **wajib** |
| Pagi keluar | opsional (boleh kosong) |
| Siang masuk | opsional (boleh kosong) |
| Siang keluar | **wajib** |

- **Hari penuh** = pagi masuk dan siang keluar terisi.
- **Setengah hari** = hanya satu sesi lengkap:
  - pagi masuk + pagi keluar (pulang siang); atau
  - siang masuk + siang keluar (datang siang).

  Ini tafsiran saya untuk kasus "datang siang / pulang siang" yang Anda sebut sebelumnya.
- **Kombinasi lain** (misalnya hanya pagi masuk) = tidak lengkap, dan form menolak
  menyimpannya bila kehadiran `hadir`.
- **Aturan ini mengubah perilaku kode lama.** `RekapAbsensi::jenisKehadiran()` diubah,
  karena sekarang hari penuh mensyaratkan keempat kolom. Test di `RekapAbsensiTest`
  disesuaikan di tahap 5.

### Format baku logbook (keputusan 3)

- Kolomnya: No, Tanggal, Pagi (Masuk/Keluar), Siang (Masuk/Keluar), Activity, Output,
  Lokasi, Action.
- Logbook baru di website dan hasil impor sama-sama memakai format ini.
- **Impor sheet September dst.:** kolom dipetakan langsung.
- **Impor sheet format lama (Juni–Agustus):**
  - Keterangan → Activity;
  - Output dan Lokasi dikosongkan;
  - Paraf `v` → `diterima`, tanpa paraf → `menunggu`;
  - keterangan berisi Sabtu/Minggu/Libur/Cuti → `libur`, berisi sakit → `sakit`, berisi
    izin → `izin`.
- **Jam yang tidak terbaca** (`??`, `8;10`, angka tanggal) dikosongkan dan ditandai di
  laporan impor untuk dicek admin.

### Catatan pertanyaan sebelumnya (arsip)

1. **Aturan hari penuh bertentangan dengan cara Excel diisi** (temuan 4). Mana yang dipakai?
   - **A (rekomendasi):**
     - **hari penuh** = ada jam **pagi masuk** dan **siang keluar**; pagi keluar dan siang
       masuk opsional (jam istirahat);
     - **setengah hari** = hanya sesi pagi (pagi masuk + pagi keluar, pulang siang) atau
       hanya sesi siang (siang masuk + siang keluar, datang siang).

     Pilihan ini sesuai data Excel, tapi `RekapAbsensi::jenisKehadiran()` dan test-nya
     perlu disesuaikan.
   - B: tetap aturan sekarang, yaitu keempat kolom wajib terisi untuk hari penuh. Peserta
     harus mengisi jam istirahat, dan riwayat Excel akan terhitung "tidak lengkap".
   - C: berdasarkan total jam kerja, misalnya ≥ 7 jam = penuh dan ≥ 3,5 jam = setengah
     (batas diatur admin).
2. **Hari mana yang dihitung uang saku?**
   - **A (rekomendasi):** semua hari `hadir` yang tidak `ditolak`; `menunggu`/`review` tetap
     dihitung. Ini sesuai Excel September (20 hari dihitung walau sebagian masih "Review").
   - B: hanya hari yang sudah `diterima`, sehingga admin wajib memverifikasi sebelum uang
     saku keluar.
   - C: semua hari `hadir` apa pun status verifikasinya.
3. **Riwayat sheet lama (Juni–September) diapakan?**
   - **A (rekomendasi):** impor sekali ke database untuk kedua format sheet (tahap 6), lalu
     aplikasi tidak membaca Google Sheets lagi. Hasilnya satu sumber data, dan laporan,
     monitoring, dan uang saku langsung seragam.
   - B: tetap baca Google Sheets langsung untuk bulan lama. Hanya format baru (September
     dst.) yang terbaca; Juni–Agustus tidak.
   - C: mulai bersih tanpa riwayat lama.
4. **Penilaian disesuaikan dengan Excel bagaimana?**
   - **A (rekomendasi):** ikuti struktur Excel tapi bisa diatur Super Admin. Kelompok dan
     bobot kelompok (bawaan Hard 60% / Soft 40%), daftar aspek, dan tabel predikat diatur
     dari halaman Aspek Penilaian; ditambah 3 isian kesimpulan dan cetak PDF.
   - B: ikuti Excel persis dan dikunci di kode (lebih cepat, tapi perubahan regulasi butuh
     coding).
   - C: biarkan seperti sekarang.
5. **Syarat surat keterangan bisa diterbitkan?**
   - **A (rekomendasi):** draft otomatis saat status `selesai`; admin menekan Terbitkan.
     Syaratnya biodata disetujui, penilaian sudah diisi, dan feedback sudah dikirim.
   - B: sama dengan A, tapi feedback tidak wajib.
   - C: terbit otomatis tanpa tinjauan admin begitu status `selesai` dan syarat A terpenuhi.

**Asumsi** (dipakai bila Anda tidak keberatan):

- peserta login dengan email;
- hadir di Sabtu/Minggu/hari libur dihitung uang saku dengan tarif yang sama;
- bukti sakit **wajib**, bukti izin opsional;
- batas isi mundur bawaan 7 hari;
- bukti jpg/png/pdf, maksimal 3 file per hari, masing-masing 2 MB;
- Pengaturan Magang bisa diubah admin dan super admin, dan setiap perubahan tercatat di Log
  Aktivitas.

---

## 8. Revisi 4: Keamanan dan Fitur Tambahan

Sumber: hasil review website oleh pemilik proyek, 25 Oktober 2026. Fase 0 dikerjakan
**paling awal** dan terpisah dari fitur baru. Pertanyaan yang masih perlu diputuskan ada di
[8.9](#89-pertanyaan-revisi-4).

### 8.1 Hasil pemeriksaan route berbasis ID

Ada 106 route. Yang diperiksa adalah setiap route yang membuka data atau file berdasarkan ID
atau parameter di URL. Pemeriksaan dilakukan dengan membaca kode dan menjalankan
`AksesRouteTest` (107 test lulus).

**Temuan utama: `/pengajuan/{id}/dokumen` (`admin.pengajuan.dokumen`)**

- Route ini sudah dijaga `auth` + `role:admin,super_admin`:
  - tamu diarahkan ke `/login`;
  - peserta mendapat 403.
- Saat login sebagai admin, mengganti angka 3 menjadi 2 memang membuka dokumen orang lain.
  Ini **sesuai aturan**, karena admin dan super admin boleh membuka semua dokumen.
- Yang perlu diperbaiki:
  1. Path `/pengajuan/...` terlihat seperti halaman publik. Path dipindah ke
     `/admin/pengajuan/...`; **nama route tetap** `admin.pengajuan.*`.
  2. Belum ada lapisan kedua. Tambah `PengajuanMagangPolicy` (`view`, `update`, `delete`)
     dan `Gate::authorize` di controller, supaya akses tetap tertolak bila suatu saat ada
     salah konfigurasi middleware.
  3. Pendaftar sama sekali belum bisa melihat dokumennya. Akan ditambah signed URL
     (lihat 8.2).
- Kode pengajuan `MG-000003` = ID 3 dengan angka nol di depan, jadi **mudah ditebak**. Kode
  ini tidak boleh dipakai sebagai satu-satunya kunci akses.

**Daftar route lain**

| Route | Parameter | Penjaga sekarang | Hasil | Tindakan |
|---|---|---|---|---|
| `admin.pengajuan.show/status/peserta/destroy` | `{pengajuanMagang}` | role admin & super admin | Aman | Tambah policy (lapisan kedua) |
| `peserta.logbook.bukti` | `{logbookBukti}` | `LogbookHarianPolicy@view` (pemilik/admin) | Aman | Tambah test peserta lain → 403 |
| `peserta.logbook.edit/update/destroy` | `{tanggal}` | query dibatasi `peserta_magang_id` milik sendiri | Aman | – |
| `hapus_bukti[]` di `peserta.logbook.update` | ID di form | dibatasi `$logbook->buktis()` | Aman | Tambah test ID bukti milik orang lain diabaikan |
| `peserta.izin.destroy`, `peserta.izin.bukti` | `{izinPeserta}` | `IzinPesertaPolicy@view` | Aman | Pakai ability `delete` terpisah untuk hapus; tambah test |
| `superadmin.peserta-magang.*` (biodata, logbook, penilaian, feedback, surat, akun, penempatan, impor) | `{pesertaMagang}` | role admin & super admin | Aman | – |
| `peserta-magang.izin.verifikasi` | `{pesertaMagang}/{izinPeserta}` | cek izin milik peserta (404) | Aman | – |
| `peserta-magang.logbook-harian.verifikasi` | ID logbook di form | dibatasi `peserta_magang_id` | Aman | – |
| `superadmin.logbook-bukti.show`, `superadmin.izin-bukti.show` | `{logbookBukti}`, `{izinPeserta}` | role admin & super admin | Aman | Tambah `Gate::authorize('view')` agar sama dengan route peserta |
| `superadmin.pengguna.edit/update/destroy` | `{user}` | super admin + `can:kelola-pengguna`, akun peserta → 404 | Aman | – |
| `banners`, `divisi`, `tarif`, `libur` | ID data pengaturan | role admin & super admin | Aman | – |
| `storage.local` (bawaan Laravel) | path file | wajib signature | Aman, tapi tidak dipakai | Matikan `serve` di disk `local` agar file privat hanya lewat controller |
| `/login` | – | tanpa `guest` | Bukan celah | Pengguna yang sudah login diarahkan ke dashboardnya |

**Kesimpulan:** tidak ditemukan route yang bisa dibuka tamu atau peserta untuk data orang
lain. Perbaikan Fase 0 bersifat penguatan (policy, path, signed URL, `serve` dimatikan) dan
menambah test pembuktian.

### 8.2 Fase 0: Perbaikan keamanan

**Aturan akses dokumen pengajuan**

| Siapa | Boleh membuka | Cara |
|---|---|---|
| Admin, Super Admin | semua dokumen | `admin.pengajuan.dokumen` + policy |
| Pendaftar (tanpa akun) | dokumennya sendiri | tautan signed URL dari hasil Cek Status, berlaku 30 menit |
| Lainnya | – | 403 (login tanpa hak, signature salah, atau kedaluwarsa) |

**Route**

| Method | Path | Nama | Catatan |
|---|---|---|---|
| GET | `/admin/pengajuan/{pengajuanMagang}/dokumen` | `admin.pengajuan.dokumen` (tetap) | path pindah dari `/pengajuan/...`; semua route `admin.pengajuan.*` ikut pindah, namanya tetap |
| GET | `/pengajuan/dokumen/{pengajuanMagang}` | `pengajuan.dokumen` (baru) | middleware `signed` + `throttle`; dibuat dengan `URL::temporarySignedRoute()` |

Route publik `pengajuan.store` dan `pengajuan.status` tetap di `/pengajuan`.

**Perubahan kode**

- `app/Policies/PengajuanMagangPolicy.php` (baru).
- `PengajuanMagangController`: `Gate::authorize` di `show`, `dokumen`, `updateStatus`,
  `buatPeserta`, `destroy`.
- `PendaftaranController::dokumen()` (baru) dan link "Lihat dokumen" di hasil Cek Status
  (`website/partials/cek-status.blade.php`).
- `LogbookHarianController::bukti/buktiIzin`: `Gate::authorize('view')`.
- `IzinPesertaPolicy@delete`.
- `config/filesystems.php`: `local.serve = false`.
- `AuthController::showLogin`: pengguna login diarahkan ke `routeDashboard()`.

**Test baru (`tests/Feature/KeamananAksesDataTest.php`)**

- Tamu dan peserta tidak bisa membuka `admin.pengajuan.show/dokumen` (redirect login / 403).
- Signed URL pendaftar:
  - berlaku untuk dokumennya;
  - ditolak bila ID diganti (signature tidak cocok), kedaluwarsa, atau tanpa signature.
- Peserta A tidak bisa membuka:
  - bukti logbook peserta B;
  - bukti izin peserta B;
  - izin peserta B (lihat/hapus).
- `hapus_bukti[]` berisi ID milik peserta B tidak menghapus apa pun.
- File di `storage/app/private` tidak bisa dibuka lewat URL langsung.

Database: tidak ada perubahan.

### 8.3 Login satu pintu

Kondisi sekarang: hanya ada satu route `GET/POST /login`, dan redirect sudah sesuai role
(`User::routeDashboard()`). Tidak ada route login lain yang perlu dihapus.

Perubahan:

- teks tombol navbar "Masuk Peserta" menjadi **Login**, tetap ke `route('login')`;
- tambah tautan "Sudah diterima? Login di sini" di bagian Daftar
  (`website/partials/pendaftaran.blade.php`);
- `/login` saat sudah login → dashboard sesuai role (lihat 8.2);
- test: tidak ada route lain ber-URI `login`, dan setiap role diarahkan ke dashboardnya.

### 8.4 Foto profil

| Hal | Rancangan |
|---|---|
| Penyimpanan | Kolom baru `users.foto_path` (nullable), file di disk privat `local` folder `foto-profil/` |
| Proses | Service `FotoProfil`: validasi jpg/png maksimal 2 MB, potong tengah jadi persegi, ubah ukuran 400×400, simpan ulang sebagai JPG (sekaligus membuang data EXIF/lokasi). Memakai ekstensi GD yang sudah aktif, **tanpa paket baru** |
| Peserta | Field "Pas foto" di form biodata otomatis menjadi foto profil (lihat pertanyaan 2). Boleh diganti kapan saja, juga setelah biodata disetujui, tanpa persetujuan ulang. Setiap penggantian dicatat di Log Aktivitas (`ganti_foto_profil`) |
| Admin & Super Admin | Upload/hapus foto di halaman Profil |
| Tampil di | sidebar & topbar (`layouts/dashboard`), tabel Data Peserta, Monitoring, detail peserta. Tanpa foto → inisial seperti sekarang |
| Akses file | Pemilik foto, admin, dan super admin. Selain itu 403 |

Route baru:

| Method | Path | Nama |
|---|---|---|
| PUT | `/profil/foto` | `profil.foto` |
| DELETE | `/profil/foto` | `profil.foto.hapus` |
| GET | `/foto-profil/{user}` | `foto-profil.show` (auth + policy, `Cache-Control: private`) |

Komponen Blade `<x-avatar :user="..." />` dipakai di semua tempat yang menampilkan inisial.

### 8.5 Logbook: hak ubah

Aturan ini **sudah berjalan** sejak Fase 12:

- peserta boleh mengubah logbook miliknya selama belum Diterima (`bisaDiubahPeserta()`);
- status verifikasi tidak ada di `SimpanLogbookHarianRequest`, jadi hanya admin yang bisa
  mengubahnya lewat `peserta-magang.logbook-harian.verifikasi`;
- setelah peserta mengubah data yang Ditolak atau Review, statusnya kembali ke Menunggu.

Yang ditambah hanya test pembuktian:

- peserta mengirim `status_verifikasi=diterima` di form → diabaikan;
- logbook Diterima → `edit/update/destroy` ditolak.

Label "Review" dan "Menunggu" tetap dua status berbeda.

### 8.6 Dashboard peserta

Service baru `RingkasanPeserta` merangkai data yang sudah ada (`KalenderLogbook`,
`PeriodeMagang`, `BiodataPeserta`, `FeedbackPeserta`, `SuratKeterangan`). Tidak ada tabel
baru.

| Kartu | Isi |
|---|---|
| Logbook hari ini | Sudah/belum diisi + tombol **Isi sekarang** ke `peserta.logbook.edit` (tanggal hari ini). Hari libur → "Hari libur" |
| Ringkasan periode berjalan (28–27) | Hadir penuh, setengah hari, izin/sakit, perkiraan uang saku (tarif sesuai tanggal) |
| Perlu diperbaiki | Logbook Ditolak/Review beserta catatan admin, tautan ke tanggalnya |
| Biodata | Progres % dan status (Draft / Menunggu tinjauan / Perlu revisi / Disetujui) |
| Penempatan | Divisi dan mentor periode ini, sisa hari magang |
| Kalender mini | Satu periode, warna per status (Diterima, Menunggu/Review, Ditolak, Izin/Sakit, Libur, Belum diisi, Terkunci) |
| Pengingat | Feedback sudah dibuka (dan sisa hari sebelum terkunci); surat keterangan sudah terbit |

Tambahan usulan:

- **Hampir terkunci:** tanggal yang belum diisi dan akan melewati batas isi mundur dalam
  2 hari ke depan.
- **Izin menunggu verifikasi:** jumlah dan tautannya.
- **Tombol cepat:** Ajukan Izin/Sakit, Unduh rekap PDF.

### 8.7 Sidebar dan navbar publik

**Sidebar Admin / Super Admin**

| Grup | Menu | Halaman |
|---|---|---|
| Utama | Dashboard, Monitoring | sudah ada |
| Kelola Peserta | Pengajuan, Data Peserta | sudah ada |
| | Biodata, Logbook & Absensi, Penilaian, Feedback, Surat Keterangan | Feedback sudah ada; sisanya halaman daftar baru (pertanyaan 3) |
| | Divisi | sudah ada, dipindah ke grup ini |
| Laporan | Rekap Uang Saku | tautan ke `superadmin.laporan.index` tab Kehadiran & Uang Saku (tanpa halaman baru) |
| | Laporan & Export | sudah ada |
| Konten Website | Profil Perusahaan, Banner, Konten | sudah ada |
| | Alumni, FAQ | baru (8.8) |
| Sistem (khusus Super Admin) | Kelola Pengguna, Aspek Penilaian, Log Aktivitas | sudah ada |
| | Pengaturan (tarif, hari libur, batas isi logbook) | sudah ada sebagai Pengaturan Magang (pertanyaan 4) |

**Sidebar Peserta**

| Grup | Menu |
|---|---|
| Utama | Dashboard |
| Data Saya | Biodata |
| Kegiatan | Logbook Harian, Ajukan Izin/Sakit, Rekap Kehadiran |
| Akhir Magang | Feedback, Surat Keterangan |
| Akun | Profil |

- **Rekap Kehadiran** adalah halaman baru `GET /peserta/rekap` (`peserta.rekap`). Halaman
  ini memakai partial rekap yang sudah ada, ditambah pilihan periode dan tombol PDF.
- "Ajukan Izin/Sakit" ditambahkan karena menu itu ada di kode dan sekarang hanya bisa
  dibuka dari halaman logbook.

**Navbar halaman publik**

`Beranda · Tentang · Alumni · FAQ · Daftar Magang · Cek Status · Login`

- **Tentang** → bagian Persyaratan + Alur, digabung di bawah `id="tentang"`.
- Pengumuman dan Kontak tetap ada di halaman (Kontak di footer), tetapi keluar dari navbar.
- Alumni dan FAQ hanya tampil di navbar bila ada data yang ditampilkan.
- Breakpoint 1200px/1024px dari Fase 19 tetap dipakai.

### 8.8 Fitur baru: FAQ dan Alumni

**FAQ**

Tabel `faqs`:

| Kolom | Tipe |
|---|---|
| `id` | bigint |
| `pertanyaan` | string 255 |
| `jawaban` | text |
| `kategori` | string 100, nullable |
| `urutan` | unsigned int |
| `tampil` | boolean, default true |
| timestamps | |

| Method | Path | Nama |
|---|---|---|
| resource (index, store, update, destroy) | `/superadmin/faq` | `superadmin.faq.*` |
| PUT | `/superadmin/faq/{faq}/urutan` (`arah` = naik/turun) | `superadmin.faq.urutan` |

Dikerjakan di satu halaman (tambah di atas, ubah di daftar yang bisa dibuka-tutup), mengikuti
pola halaman Divisi.

- Halaman publik: bagian `#faq` di beranda, berupa accordion `<details>` (tanpa library),
  dikelompokkan per kategori bila ada.
- 5 FAQ awal dibuat oleh migrasi `create_faqs_table`, bukan seeder, supaya ikut terbentuk
  saat deploy.

**Alumni**

Tabel `alumnis`:

| Kolom | Tipe | Catatan |
|---|---|---|
| `id` | bigint | |
| `peserta_magang_id` | FK nullable, unique, `nullOnDelete` | kosong untuk alumni manual |
| `nama` | string | |
| `foto_path` | string nullable | disk `public` folder `alumni/` (hanya foto yang disetujui tampil) |
| `universitas`, `program_studi`, `divisi` | string | |
| `periode` | string | contoh "Juni – November 2026" |
| `rating` | tinyint nullable (1–5) | hanya untuk alumni manual |
| `judul_project` | string 150 | |
| `deskripsi_project` | text | |
| `testimoni` | text nullable | |
| `urutan` | unsigned int | |
| `tampil` | boolean, default false | |
| timestamps | | |

Tabel `feedback_pesertas` + kolom `bersedia_tampil_alumni` (boolean, default false).

- **Rating:** dari jawaban feedback pertanyaan skala no. 10 ("Secara keseluruhan saya puas…",
  skala 1–5), dibaca langsung dari feedback sehingga selalu sesuai. **Bukan** dari nilai
  penilaian. Alumni manual memakai kolom `rating`.
- **Persetujuan:**
  - form feedback mendapat centang "Saya bersedia ditampilkan sebagai alumni" (tetap bisa
    diubah selama feedback belum terkunci);
  - alumni dari peserta hanya tampil bila centang itu aktif **dan** `tampil = true`;
  - bila peserta menarik persetujuan, alumni otomatis disembunyikan.
- **Dari peserta:**
  - admin memilih dari daftar peserta yang `selesai` dan bersedia;
  - nama, foto profil, universitas, prodi, divisi terakhir, periode, dan testimoni (dari
    jawaban "paling disukai") terisi otomatis dan boleh disunting;
  - admin melengkapi judul dan deskripsi project.
- **Manual:** semua field diisi admin, untuk alumni sebelum sistem ini ada.
- Admin bisa mengatur urutan dan menyembunyikan alumni. Setiap tambah/ubah/hapus dicatat di
  Log Aktivitas.

| Method | Path | Nama |
|---|---|---|
| resource (tanpa show) | `/superadmin/alumni` | `superadmin.alumni.*` |
| PUT | `/superadmin/alumni/{alumni}/urutan` (`arah` = naik/turun) | `superadmin.alumni.urutan` |

Memilih dari peserta memakai `superadmin.alumni.create?peserta={id}` (form terisi otomatis),
bukan route `dari-peserta` terpisah. Foto alumni ada di disk `public`, jadi server butuh
`php artisan storage:link`.

- Halaman publik: bagian `#alumni` di beranda, berupa carousel kartu (CSS scroll-snap +
  tombol geser, tanpa library baru).
- Klik kartu → popup `<dialog>` berisi deskripsi project dan testimoni.
- Bisa dipakai dengan keyboard; di HP digeser dengan jari.

### 8.9 Pertanyaan revisi 4

**Keputusan (25 Oktober 2026): 1A, 2A, 3A, 4B, 5A**, semuanya mengikuti rekomendasi.

1. **Cek status pendaftar memakai apa?**
   Sekarang memakai NIM + email. Kode `MG-000003` hanya ID berurutan, jadi mudah ditebak.
   - **A (rekomendasi):** kode MG + email. Hasil cek status menampilkan tautan dokumen
     (signed URL 30 menit).
   - B: tetap NIM + email (kode MG hanya ditampilkan), dengan tautan dokumen yang sama.
   - C: kode MG saja (tidak disarankan, karena orang lain bisa menebak kode).
2. **Pas foto di biodata.**
   Biodata sekarang **belum punya** field pas foto.
   - **A (rekomendasi):** tambah field pas foto di form biodata yang disimpan sebagai foto
     profil (`users.foto_path`), dan **wajib** ikut hitungan progres biodata.
   - B: sama dengan A, tapi opsional (tidak memengaruhi progres).
   - C: foto hanya diunggah dari halaman Profil, tidak ada di biodata.
3. **Menu Biodata, Logbook & Absensi, Penilaian, Surat Keterangan di sidebar admin.**
   Sekarang keempatnya hanya ada per peserta (di detail peserta).
   - **A (rekomendasi):** buat halaman daftar ringan per menu. Isinya semua peserta beserta
     statusnya (misalnya biodata menunggu tinjauan, logbook belum diverifikasi) dengan
     tautan ke halaman per peserta.
   - B: keempat menu mengarah ke Data Peserta dengan filter, tanpa halaman baru.
4. **Pengaturan Magang khusus Super Admin?**
   Sekarang admin juga bisa mengubah tarif, hari libur, dan batas isi logbook.
   - **A:** ikut usulan, hanya Super Admin. Admin kehilangan akses (Divisi tetap bisa
     dikelola admin).
   - **B (rekomendasi):** Hari Libur tetap boleh diatur admin (sering berubah), sedangkan
     tarif dan batas isi hanya Super Admin.
   - C: tetap seperti sekarang (admin & super admin), hanya letak menunya di grup Sistem.
5. **Foto alumni dari peserta.**
   - **A (rekomendasi):** foto profil peserta disalin ke disk publik saat alumni ditampilkan,
     dan dihapus bila disembunyikan atau persetujuan ditarik.
   - B: admin selalu mengunggah foto alumni terpisah (foto profil tidak pernah dipakai di
     halaman publik).

### 8.10 Urutan pengerjaan revisi 4

| Tahap | Branch | Isi | Bergantung pada |
|---|---|---|---|
| R0 ✅ | `fase-0-keamanan` | Policy pengajuan, pindah path admin, signed URL pendaftar, `serve` dimatikan, test keamanan | – |
| R1 ✅ | `fase-20-login-sidebar` | Login satu pintu, sidebar baru per grup, halaman daftar admin (pertanyaan 3), Rekap Kehadiran peserta, hak Pengaturan (pertanyaan 4) | R0 |
| R2 ✅ | `fase-21-foto-profil` | `users.foto_path`, service `FotoProfil`, pas foto di biodata, `<x-avatar>` | R0 |
| R3 ✅ | `fase-22-dashboard-peserta` | `RingkasanPeserta`, kartu dashboard, kalender mini, test logbook (8.5) | R1 |
| R4 ✅ | `fase-23-faq` | Tabel, CRUD, accordion publik | R1 |
| R5 ✅ | `fase-24-alumni` | Tabel, centang di feedback, CRUD + dari peserta, carousel + popup, navbar publik baru | R2, R4 |

- Setiap tahap memperbarui matriks `AksesRouteTest` dan `docs/PANDUAN-UJI-COBA.md`.
- Fase 7 (deploy) tetap dikerjakan setelah R5.
- Tidak ada dependency baru.


## 9. Revisi 5: PILARA, Perbaikan, Tanda Tangan, UI/UX, Dokumentasi, Panduan

Dikerjakan tahap demi tahap. Setelah tiap tahap: semua test dijalankan, hasil dilaporkan,
lalu menunggu konfirmasi pemilik sebelum lanjut.

### 9.1 Identitas produk

- **Nama:** PILARA (dari nilai PILAR: Profesional, Integrity, Loyalty, Accountability, Responsibility).
- **Tagline:** "Tumbuh bersama, kokoh berkarya."
- **Deskripsi:** "PILARA adalah platform magang PT Pilar Teknotama Sinergi (PITSI) untuk pendaftaran,
  biodata, logbook harian, absensi dan uang saku, penilaian, feedback, surat keterangan, hingga alumni."
- Nama lama (Magenta) diganti di APP_NAME, judul halaman, header, footer, email, PDF, dan README.
  Nama database, folder, dan namespace kode **tidak** diubah.
- Logo: `public/images/logo-pitsi.png`, dipakai di navbar publik, login, sidebar, dan kop PDF.
  (Per 25-10-2026 file ini belum ada di repo; perlu disalin pemilik sebelum Tahap 3.)

### 9.2 Palet warna (token, ditulis sekali)

| Token | Nilai | Pemakaian |
|---|---|---|
| `--latar` | `#F7F9FB` | latar halaman |
| `--permukaan` | `#FFFFFF` | kartu, tabel, form |
| `--utama` | `#2688B4` | warna utama |
| `--utama-gelap` | `#0B6A9C` | judul, hover, menu aktif |
| `--aksen` | `#EE7623` | sangat terbatas: tombol "Daftar Magang", badge penting |
| `--teks` | `#1F2937` | teks |
| `--teks-sekunder` | `#6B7280` | teks sekunder |

### 9.3 Tahap 1: Perbaikan bug (`fase-25-perbaikan-bug`) ✅

**a. Foto alumni tidak tampil.**
- Penyebab: `Alumni::urlFoto()` memakai `Storage::disk('public')->url()`, yang membentuk URL dari
  `APP_URL` (`http://localhost:8000`). Bila web dibuka dari alamat lain (Laragon `.test`, port 8010,
  atau server), gambar mengarah ke host yang salah sehingga tidak muncul. `storage:link` dan file
  sudah benar; banner tidak terkena karena memakai `asset('storage/...')`.
- Perbaikan: URL foto alumni memakai `asset()` (mengikuti alamat yang sedang dibuka), sama seperti banner.
- Pratinjau sebelum disimpan: satu skrip umum di layout dashboard untuk
  `<input type="file" data-pratinjau="...">`, dipakai di foto alumni, foto profil, dan pas foto
  biodata (nanti juga dokumentasi). Pratinjau juga memberi peringatan bila file bukan JPG/PNG atau
  lebih dari 2 MB.

**b. PDF penilaian lebih dari 1 halaman.**
- Selalu 1 halaman A4: margin dan huruf dirapatkan, hard skill dan soft skill berdampingan, tabel
  predikat diringkas, blok tanda tangan `page-break-inside: avoid`.
- Kolom kesimpulan dibatasi 600 karakter dan 5 baris (sebelumnya 2000), plus penghitung karakter di form.
  Baris baru dari browser (CRLF) dihitung 1 karakter. Penilaian lama yang lebih panjang perlu diringkas saat disimpan ulang.
- Test: PDF dengan isian terpanjang yang diizinkan (satu paragraf penuh dan 5 paragraf yang terlipat) harus 1 halaman.

### 9.4 Tahap 2: Tanda tangan dokumen (`fase-26-tanda-tangan`) ✅

Keputusan: **1A, 2A, 3A**.

- Super Admin mengatur nama, jabatan, gambar tanda tangan, dan stempel di Pengaturan Magang.
  Semuanya tercetak otomatis di PDF penilaian dan surat keterangan.
- **1A:** Mengetahui dan Menyetujui masing-masing punya gambar tanda tangan; stempel satu, di
  Menyetujui. Surat keterangan memakai Menyetujui + stempel. Kolom Penilai (mentor) tetap kosong
  untuk tanda tangan basah.
- Gambar disimpan di disk privat (`tanda-tangan/`) sebagai PNG (transparansi dipertahankan,
  diperkecil maks 600×300 / stempel 400×400), disisipkan ke PDF sebagai data URI. Hanya Super Admin
  yang bisa melihat dan mengubahnya (`kelola-pengaturan-magang`).
- Surat terbit menyalin path gambar ke datanya, jadi mengganti tanda tangan tidak mengubah surat
  lama. File lama tidak dihapus selama masih dipakai surat terbit.
- **2A:** QR dibuat di server dengan `chillerlan/php-qrcode` (dependency baru, disetujui).
- QR mengarah ke `/verifikasi-surat/{kode}` (publik, `throttle:30,1`, kode acak 32 karakter) yang
  hanya menampilkan nama, periode, dan nomor surat. Draft atau kode salah → "Surat tidak ditemukan".
- **3A:** surat yang sudah terbit sebelum fitur ini langsung diberi kode lewat migration, sehingga
  QR ikut tercetak saat diunduh ulang. Karena datanya belum menyimpan gambar, tanda tangan dan
  stempel hanya dipakai bila nama penanda tangannya masih sama dengan pengaturan sekarang.

### 9.5 Tahap 3: Upgrade UI/UX (`fase-27-ui-pilara`) ✅

Keputusan ikon: **Lucide** (lisensi ISC), outline, garis 1.75, tanpa emoji.

- **Token warna** satu sumber di `resources/views/partials/tema.blade.php` (palet 9.2 + turunan
  netral dan status). Dipakai dashboard, login, verifikasi surat, dan website publik (variabel lama
  website `--primary` dst. diarahkan ke token). Sekitar 1.070 kode warna lama di halaman dashboard
  diganti `var(--…)`. PDF tetap memakai kode warna langsung (dompdf tidak mendukung `var()`).
- **Ikon:** komponen `<x-icon name="…">` (SVG inline, warna `currentColor`). Sidebar, kartu
  statistik, alert, pagination, tombol unduh/cetak, dan website publik tidak lagi memakai emoji.
  Bintang rating dan panah teks (→) tetap sebagai teks.
- **Sidebar** terang: teks dan ikon abu netral, hanya menu aktif berwarna utama; logo PITSI dan
  tagline. Di layar ≤ 960 px sidebar menjadi panel geser (tombol menu di topbar, overlay, tombol Esc).
- **Seragam:** huruf Plus Jakarta Sans di semua halaman, komponen tombol/form/alert/badge/pagination
  dari token, badge status per nada (menunggu/diajukan = kuning, disetujui/aktif/terbit = hijau,
  ditolak = merah, selesai = biru), komponen `<x-empty>` untuk data kosong, judul halaman lama
  disamakan 17 px.
- **Aksen oranye** hanya untuk tombol "Daftar Magang" dan badge jumlah di sidebar; dekorasi lain
  memakai warna utama. Tombol Login navbar memakai warna utama.
- **Identitas PILARA:** `APP_NAME=PILARA` (.env, .env.example, bawaan config), judul tab, sidebar,
  login, navbar dan footer publik, footer laporan, judul halaman cetak. Logo versi kecil
  `logo-pitsi-128.png` (web, favicon) dan `logo-pitsi-256.png` (PDF) dibuat dari logo asli. Kop PDF
  penilaian, logbook, surat keterangan, dan laporan memakai logo.
- Tidak diubah: nama database, folder, namespace, email akun uji di seeder (hanya nama tampilannya).
- Catatan: mengganti `APP_NAME` mengganti nama cookie sesi, jadi semua pengguna perlu login ulang
  sekali.

### 9.6 Tahap 4: Galeri dokumentasi (Beranda)

- Thumbnail 2 baris ke bawah, sisanya memanjang ke kanan dan bisa digeser (seperti carousel alumni).
- Hover: lapisan transparan berisi judul, tanggal, penjelasan singkat. Di HP muncul saat disentuh.
- Klik untuk memperbesar (lightbox).
- Dikelola admin dan super admin: upload, judul, keterangan, tanggal kegiatan, urutan,
  tampil/sembunyikan. Masuk grup sidebar "Konten Website".

**Hasil (`fase-28-dokumentasi`) ✅**

- Tabel `dokumentasis` (judul ≤ 150, keterangan ≤ 300, tanggal kegiatan, foto + thumbnail, urutan,
  tampil). Model `Dokumentasi`, `DokumentasiController` (resource tanpa show + `urutan` + `tampil`),
  `SimpanDokumentasiRequest`, service `FotoDokumentasi`.
- Foto JPG/PNG ≤ 5 MB, minimal 300×200; disimpan ulang sebagai JPG di disk public (EXIF terbuang,
  diputar sesuai orientasi HP) dalam dua ukuran: besar ≤ 1600 px (lightbox) dan thumbnail ≤ 640 px.
  Foto lama dihapus saat diganti atau dokumentasi dihapus.
- Beranda: bagian "Kegiatan Magang" (`#dokumentasi`) di antara Alur dan Alumni, hanya tampil bila
  ada foto yang ditampilkan. Grid 2 baris dengan kolom memanjang ke kanan (4 kolom terlihat di
  desktop, 3 di tablet, ±1,5 di HP), tombol geser, overlay gradasi saat hover/fokus, lightbox
  `<dialog>` dengan tombol dan panah keyboard. Di layar sentuh: sentuh pertama menampilkan
  keterangan, sentuh kedua memperbesar.
- Navbar tidak ditambah menu baru (tetap sesuai 8.7).
- Ikut diperbaiki: tombol Login navbar sebelumnya hilang di lebar 1025–1200 px.

### 9.7 Tahap 5: Buku panduan dan buku alur (terakhir, setelah UI final)

- Menu "Panduan" di web sesuai role yang login, plus file Markdown di `docs/panduan/`.
- Panduan per aktor (Peserta, Admin, Super Admin) langkah demi langkah dengan screenshot.
- Buku alur lengkap dengan diagram: daftar → cek status → admin menyetujui → data peserta dan akun
  dibuat → login dan ganti password → biodata dan verifikasi → logbook harian dan verifikasi →
  rekap uang saku per periode (28–27) → rotasi divisi → feedback aktif 1 bulan sebelum selesai →
  penilaian → status selesai → surat keterangan dibuat dan ditandatangani → alumni (jika bersedia).

**Hasil (`fase-29-panduan`) ✅**

- `docs/panduan/`: `README.md`, `alur-magang.md` (diagram Mermaid + tabel 19 langkah + aturan
  penting), `panduan-peserta.md`, `panduan-admin.md`, `panduan-super-admin.md`.
- 33 screenshot di `public/images/panduan/` (JPG, ±3,3 MB) dari **data contoh fiktif**
  (`PanduanDemoSeeder`, database terpisah), bukan data asli. Skrip pengambil ulang:
  `docs/panduan/alat/ambil_screenshot.py`.
- Menu **Panduan** (`/panduan/{bagian?}`, nama route `panduan`) untuk semua role: peserta → Peserta
  + Alur; admin → Admin + Alur; super admin → Super Admin + Admin + Alur. Bagian role lain → 404.
  Isi dibaca langsung dari Markdown (`App\Services\BukuPanduan`, CommonMark bawaan Laravel, HTML
  mentah dibuang); gambar diarahkan ke `public/`, tautan antar-panduan menjadi tautan menu, dan
  diagram Mermaid diganti diagram HTML.

### 9.8 Urutan pengerjaan revisi 5

| Tahap | Branch | Isi |
|---|---|---|
| 1 | `fase-25-perbaikan-bug` | URL foto alumni, pratinjau upload, PDF penilaian 1 halaman, batas kesimpulan |
| 2 | `fase-26-tanda-tangan` | Pengaturan penanda tangan, gambar TTD + stempel, QR verifikasi surat |
| 3 | `fase-27-ui-pilara` | Identitas PILARA, token warna, ikon seragam, komponen UI, tampilan HP |
| 4 | `fase-28-dokumentasi` | Tabel + CRUD dokumentasi, galeri Beranda, lightbox |
| 5 | `fase-29-panduan` | Menu Panduan per role, `docs/panduan/`, buku alur + diagram |


## 10. Revisi 6: Isi Jam Logbook dari Kartu Absensi

Status: **dikerjakan**; keputusan pemilik di 10.9. Branch: `fase-32-scan-kartu-absensi`.
Dikerjakan setelah modul logbook stabil (Revisi 5 sudah selesai dan di-merge).

### 10.1 Latar belakang

Kantor memakai mesin ceklok dengan kartu absensi kertas (dot-matrix). Peserta memotret kartu,
sistem membaca jam dengan Gemini, peserta memeriksa lalu menyimpan.

- Satu kartu = satu bulan kalender; depan tanggal 1–15, belakang 16–31.
- Yang dipakai hanya **Pagi Masuk** → `pagi_masuk` dan **Siang Keluar** → `siang_keluar`. Lembur diabaikan.
- Ada kode tanggal kecil di depan jam (mis. "19 7:54"); cetakan sering meleset ke baris tanggal sebelah.
- Bulan ditulis tangan → tidak dibaca otomatis; peserta memilih bulan, tahun, dan sisi.

**Catatan foto contoh:** ada di `docs/reverensi/kartu-absensi-depan.webp` dan
`docs/reverensi/kartu-absensi-belakang.webp` (folder sudah di-ignore Git; format .webp, bukan .jpg).

**Bacaan manual foto contoh (kunci jawaban uji akurasi, Oktober 2026):**

| Sisi | Tanggal | Masuk | Keluar | Catatan |
|---|---|---|---|---|
| Depan | 1 | 07:57 | 16:30 | |
| Depan | 2 | 08:06 | 16:40 | |
| Depan | 5 | 08:06 | 16:36 | 3–4 Sabtu–Minggu |
| Depan | 7 | 08:04 | 16:41 | 6 kosong (Selasa) |
| Depan | 8 | 07:57 | 16:38 | |
| Depan | 9 | 08:06 | 16:42 | |
| Depan | 12 | 07:57 | 16:41 | tercetak di baris 11 (11 = Minggu) |
| Depan | 13 | 08:10 | 16:43 | tercetak di antara baris 12–13 (dikoreksi pemilik) |
| Depan | 14 | 08:02 | – | tercetak di antara baris 13–14 |
| Depan | 15 | 08:05 | 16:37 | tercetak di antara baris 14–15 |
| Belakang | 16 | 07:54 | 16:54 | |
| Belakang | 19 | 07:58 | 16:37 | 17–18 Sabtu–Minggu |
| Belakang | 20 | 07:58 | 16:35 | |
| Belakang | 21 | 08:08 | 16:37 | |
| Belakang | 22 | 07:47 | 16:45 | |
| Belakang | 23 | 08:05 | – | belum keluar saat difoto |

Baris tanggal 12–15 sisi depan adalah kasus sulit (meleset); kunci jawaban sudah dikonfirmasi pemilik.

### 10.2 Cara baca (Gemini)

- `GEMINI_API_KEY` dan `GEMINI_MODEL` di `.env` (tidak di-commit); `.env.example` hanya berisi nama kuncinya.
  Per 26-10-2026 keduanya **belum ada** di `.env`.
- Panggilan lewat `Http` facade Laravel ke REST API Gemini (tanpa package baru), timeout + retry singkat.
- Foto dikecilkan di server (sisi terpanjang ±1600 px, JPG, EXIF dibuang) sebelum dikirim.
- Prompt meminta **JSON terstruktur** (`responseMimeType: application/json` + `responseSchema`):
  `[{tanggal, jam_masuk, jam_keluar, keyakinan (0–1), catatan}]`. Prompt menjelaskan: sisi kartu dan
  rentang tanggalnya, kolom yang dipakai, kode tanggal di depan jam, cetakan bisa meleset sehingga
  harus dipasangkan ke tanggal paling masuk akal, serta **kalender bulan itu** (hari Sabtu/Minggu dan
  hari libur) sebagai petunjuk.
- Validasi server: format `HH:MM` valid, tanggal di dalam sisi kartu dan ada di bulan itu, jam masuk
  < jam keluar, tidak ada tanggal ganda, keyakinan rendah (< 0,8) atau masalah validasi → baris kuning.
- Respons mentah tidak disimpan; yang disimpan hanya hasil yang sudah divalidasi.

### 10.3 Alur peserta

1. Halaman Logbook → tombol **Isi dari kartu absensi** (aktif sesuai jendela scan).
2. Pilih bulan, tahun, sisi kartu → potret (kamera HP, `capture="environment"`) atau unggah foto.
3. Tabel pratinjau: tanggal, jam masuk, jam keluar (bisa diedit). Baris meragukan berwarna kuning
   dengan keterangan; baris terkunci (diterima/belum tiba) tampil abu-abu dan tidak bisa dipilih.
4. Bila tanggal sudah punya jam: tampil **lama vs baru**, peserta memilih per baris.
5. **Simpan** (tidak pernah otomatis). Activity, output, lokasi tetap diisi peserta seperti biasa.
6. Foto kartu menjadi bukti di logbook tanggal-tanggal yang terisi.

### 10.4 Aturan

- Tanggal **Diterima** atau **belum tiba** tidak bisa diubah scan.
- Scan boleh mengisi tanggal di luar batas isi mundur (data dari mesin) → ditandai untuk admin.
- Setiap logbook mencatat **sumber jam**: `manual` atau `scan`. Bila jam hasil scan diubah peserta,
  nilai asli scan disimpan dan admin melihat tanda **diubah setelah scan**.
- Menyimpan hasil scan mengembalikan status ke **Menunggu** (sama seperti simpan manual).
- Pengisian jam manual tetap ada.

### 10.5 Pengaturan (admin & super admin)

Di Pengaturan Magang (bagian baru "Scan Kartu Absensi", boleh diubah admin juga):

| Pengaturan | Nilai awal |
|---|---|
| Saklar fitur scan | aktif |
| Jendela sisi 1–15 | tanggal 14 s.d. 17 |
| Jendela sisi 16–31 | tanggal 28 s.d. tanggal 2 bulan berikutnya |
| Batas scan per peserta per hari | 5 |

Di luar jendela, tombol nonaktif dengan keterangan kapan bisa dipakai.

### 10.6 Sisi admin

- Verifikasi logbook: badge **Scan/Manual**, tanda **diubah setelah scan** dan **di luar batas isi
  mundur**, tombol **Lihat kartu** yang membuka foto berdampingan dengan data jam.
- LogAktivitas `scan_kartu_absensi`: peserta, waktu, sisi, bulan, jumlah baris terbaca/tersimpan.
  Tanpa API key dan tanpa respons mentah.

### 10.7 Rancangan data

- Tabel baru `scan_kartu_absensis`: peserta, bulan, tahun, sisi (`1-15`/`16-31`), foto_path (disk
  privat), status (`dipratinjau`/`disimpan`), jumlah_baris, hasil (JSON tervalidasi), model, token
  masuk/keluar (untuk menghitung biaya), dibuat_oleh, timestamps.
- `logbook_harians` ditambah: `sumber_jam` (`manual`/`scan`, bawaan manual), `scan_kartu_absensi_id`,
  `jam_scan_asli` (JSON: pagi_masuk, siang_keluar hasil scan), `di_luar_batas_isi` (boolean).
- Foto kartu: satu file per scan, ditautkan ke logbook lewat `scan_kartu_absensi_id` (tidak
  digandakan ke `logbook_buktis` dan tidak memakan jatah 3 bukti per hari). Akses lewat Policy:
  pemilik dan admin/super admin.
- `pengaturan_magangs` ditambah: `scan_aktif`, `scan_jendela_1_mulai/selesai`,
  `scan_jendela_2_mulai/selesai`, `scan_batas_harian`.

### 10.8 Test

Gemini di-mock (`Http::fake`). Validasi hasil scan, tanggal diterima/masa depan, jendela scan, batas
harian, akses foto kartu (peserta lain 403), lama vs baru, penanda diubah/di luar batas, LogAktivitas.
Uji akurasi nyata dengan dua foto contoh (memanggil Gemini sungguhan) dilaporkan terpisah, tidak
masuk test otomatis.

### 10.9 Keputusan pemilik (26-10-2026)

1. **Kunci jawaban** sisi depan 11–15: 11 kosong (Minggu), 12 = 07:57–16:41, 13 = 08:10–16:43,
   14 = 08:02 tanpa keluar, 15 = 08:05–16:37.
2. **Tanggal tanpa logbook:** logbook dibuat (Hadir, jam terisi, status Menunggu) dengan tanda
   *activity belum diisi*. Admin tidak bisa menerimanya sebelum activity diisi; dashboard
   mengingatkan peserta.
3. **Bulan yang bisa dipilih:** hanya bulan dari jendela aktif (tanggal 14–17 → sisi 1–15 bulan
   ini; tanggal 28–2 → sisi 16–31 bulan dari tanggal 28). Kartu bulan lain tetap bisa diisi manual.
4. **Tanggal Izin/Sakit:** dilewati, baris tampil kuning dengan peringatan; tidak ditimpa.

### 10.10 Urutan pengerjaan

| Langkah | Isi |
|---|---|
| 1 | Migration + pengaturan + model, service Gemini (dengan mock), validasi hasil |
| 2 | Uji akurasi dua foto contoh (Gemini sungguhan), sesuaikan prompt |
| 3 | Alur peserta: unggah/kamera → pratinjau → lama vs baru → simpan |
| 4 | Sisi admin: badge, penanda, lihat kartu; LogAktivitas |
| 5 | Test, panduan peserta/admin, laporan akurasi + perkiraan biaya |
