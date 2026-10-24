# Rencana Fitur: Memindahkan Buku Kerja Magang ke Website

Status: **rancangan revisi 3, belum dikerjakan.** Semua pertanyaan sudah dijawab (lihat
[bagian 7](#7-keputusan-revisi-2)). Coding menunggu persetujuan untuk mulai tahap 1.

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
