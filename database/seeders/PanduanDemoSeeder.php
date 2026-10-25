<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\AspekPenilaian;
use App\Models\BiodataPeserta;
use App\Models\Divisi;
use App\Models\Dokumentasi;
use App\Models\FeedbackPeserta;
use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\Mahasiswa;
use App\Models\PengajuanMagang;
use App\Models\PengaturanMagang;
use App\Models\PenilaianPeserta;
use App\Models\Perusahaan;
use App\Models\PesertaMagang;
use App\Models\ScanKartuAbsensi;
use App\Models\SuratKeterangan;
use App\Models\User;
use App\Services\FotoAlumni;
use App\Services\FotoDokumentasi;
use App\Services\FotoProfil;
use App\Services\TandaTanganDokumen;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Data contoh (fiktif) untuk screenshot buku panduan di docs/panduan.
 *
 * JANGAN dijalankan di database asli. Pakai database terpisah, misalnya:
 * DB_DATABASE=database/panduan.sqlite php artisan migrate:fresh --seeder=PanduanDemoSeeder
 *
 * Password akun peserta contoh diambil dari env PANDUAN_PASSWORD (atau acak).
 */
class PanduanDemoSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        $this->call(UserSeeder::class);

        $hariIni = CarbonImmutable::today();
        $admin = User::where('role', User::ROLE_ADMIN)->firstOrFail();
        $passwordPeserta = env('PANDUAN_PASSWORD') ?: Str::password(16);

        Perusahaan::create([
            'nama_perusahaan' => 'PT Pilar Teknotama Sinergi',
            'nama' => 'PT Pilar Teknotama Sinergi',
            'alamat' => 'Jl. Contoh Raya No. 10',
            'kota' => 'Yogyakarta',
            'no_telepon' => '0274-000000',
            'email' => 'magang@contoh.test',
            'website' => 'contoh.test',
            'deskripsi' => 'Perusahaan jasa konsultan, audit, dan penjaminan yang membuka program magang bagi mahasiswa.',
        ]);

        PengaturanMagang::ambil()->update([
            'kota_surat' => 'Yogyakarta',
            'mengetahui_nama' => 'Sari Wulandari',
            'mengetahui_jabatan' => 'Staff GA & HRD',
            'menyetujui_nama' => 'Hendra Kusuma',
            'menyetujui_jabatan' => 'Manager GA & FA',
            // Demo: jendela sisi 1–15 dibuka sepanjang bulan agar halaman scan bisa dipotret kapan saja.
            'scan_jendela_1_mulai' => 1,
            'scan_jendela_1_selesai' => 31,
        ]);

        $tandaTangan = app(TandaTanganDokumen::class);
        $tandaTangan->simpan(PengaturanMagang::ambil(), 'ttd_mengetahui', $this->tandaTangan(false));
        $tandaTangan->simpan(PengaturanMagang::ambil(), 'ttd_menyetujui', $this->tandaTangan(false));
        $tandaTangan->simpan(PengaturanMagang::ambil(), 'stempel', $this->tandaTangan(true));

        $divisi = collect(['Unit Umum', 'Divisi Konsultan', 'Divisi IT'])
            ->mapWithKeys(fn (string $nama) => [$nama => Divisi::firstOrCreate(['nama' => $nama])]);

        // Pengajuan dari website.
        foreach ([['Dimas Pratama', 'Universitas Gadjah Mada'], ['Nabila Putri', 'Universitas Negeri Yogyakarta'], ['Fajar Nugroho', 'Politeknik Negeri Semarang']] as [$nama, $kampus]) {
            PengajuanMagang::factory()->create([
                'mahasiswa_id' => Mahasiswa::factory()->create(['nama' => $nama, 'asal_kampus' => $kampus, 'email' => Str::slug($nama).'@contoh.test'])->id,
                'tanggal_mulai' => $hariIni->addWeeks(3)->toDateString(),
                'tanggal_selesai' => $hariIni->addWeeks(3)->addMonths(3)->toDateString(),
                'keterangan' => 'Ingin belajar langsung proses kerja konsultan dan audit sesuai program studi.',
            ]);
        }

        PengajuanMagang::factory()->ditolak()->create([
            'mahasiswa_id' => Mahasiswa::factory()->create(['nama' => 'Yoga Firmansyah', 'email' => 'yoga@contoh.test'])->id,
        ]);

        // Peserta aktif: Rina.
        $rina = $this->peserta('Rina Aulia', 'rina@contoh.test', $passwordPeserta, [
            'nim' => '2207110021',
            'asal_kampus' => 'Universitas Ahmad Dahlan',
            'program_studi' => 'Sistem Informasi',
            'periode_mulai' => '2026-08-03',
            'periode_selesai' => $hariIni->addWeeks(3)->toDateString(),
            'status' => 'aktif',
        ], 'perempuan', BiodataPeserta::STATUS_DIAJUKAN);

        $rina->penempatans()->create(['periode' => '2026-08', 'divisi_id' => $divisi['Unit Umum']->id, 'mentor_nama' => 'Agus Setiawan', 'mentor_jabatan' => 'Supervisor Umum']);
        $rina->penempatans()->create(['periode' => '2026-10', 'divisi_id' => $divisi['Divisi Konsultan']->id, 'mentor_nama' => 'Maya Lestari', 'mentor_jabatan' => 'Senior Consultant']);

        $this->logbook($rina, $hariIni, $admin);
        $this->scanKartu($rina, $hariIni);

        $izin = IzinPeserta::create([
            'peserta_magang_id' => $rina->id,
            'jenis' => LogbookHarian::SAKIT,
            'tanggal_mulai' => $this->hariKerjaSebelum($hariIni, 7)->toDateString(),
            'tanggal_selesai' => $this->hariKerjaSebelum($hariIni, 7)->toDateString(),
            'keterangan' => 'Demam, istirahat sesuai surat dokter.',
            'status' => LogbookHarian::DITERIMA,
            'diverifikasi_oleh' => $admin->id,
            'diverifikasi_pada' => now(),
        ]);
        // Hari sakit menggantikan logbook hadir pada tanggal itu.
        LogbookHarian::where('peserta_magang_id', $rina->id)->whereDate('tanggal', $izin->tanggal_mulai)->delete();
        LogbookHarian::create([
            'peserta_magang_id' => $rina->id,
            'tanggal' => $izin->tanggal_mulai->toDateString(),
            'kehadiran' => LogbookHarian::SAKIT,
            'izin_peserta_id' => $izin->id,
            'status_verifikasi' => LogbookHarian::DITERIMA,
        ]);

        // Peserta selesai: Bayu (sudah dinilai, feedback, surat terbit, alumni).
        $bayu = $this->peserta('Bayu Saputra', 'bayu@contoh.test', $passwordPeserta, [
            'nim' => '2105110045',
            'asal_kampus' => 'Universitas Islam Indonesia',
            'program_studi' => 'Teknik Informatika',
            'periode_mulai' => '2026-03-02',
            'periode_selesai' => '2026-08-28',
            'status' => 'aktif',
        ], 'laki-laki', BiodataPeserta::STATUS_DISETUJUI);
        $bayu->penempatans()->create(['periode' => '2026-03', 'divisi_id' => $divisi['Divisi IT']->id, 'mentor_nama' => 'Rudi Hartono', 'mentor_jabatan' => 'IT Lead']);

        $this->penilaian($bayu, $admin);

        FeedbackPeserta::create([
            'peserta_magang_id' => $bayu->id,
            'skor' => array_fill_keys(range(1, 10), 5),
            'paling_disukai' => 'Mentor sabar dan project nyata yang dipakai tim.',
            'kendala' => 'Awalnya belum terbiasa dengan istilah audit.',
            'perlu_diperbaiki' => 'Orientasi minggu pertama bisa lebih terstruktur.',
            'saran' => 'Tambahkan sesi berbagi antar peserta.',
            'rekomendasi' => 'sangat_bersedia',
            'evaluasi_keseluruhan' => 'Pengalaman magang yang sangat bermanfaat.',
            'pernyataan' => true,
            'dikirim_pada' => now(),
            'jumlah_kirim' => 1,
            'bersedia_tampil_alumni' => true,
        ]);

        $bayu->update(['status' => 'selesai']);
        $surat = $bayu->fresh()->suratKeterangan;
        $tanggalSurat = CarbonImmutable::parse('2026-08-31');
        $surat->pastikanKodeVerifikasi();
        $surat->fill([
            'status' => SuratKeterangan::TERBIT,
            'tahun' => 2026,
            'urut' => 1,
            'nomor_surat' => SuratKeterangan::formatNomor(PengaturanMagang::ambil()->format_nomor_surat, 1, $tanggalSurat),
            'tanggal_surat' => $tanggalSurat->toDateString(),
            'data' => SuratKeterangan::snapshot($bayu->fresh(), $tanggalSurat, false),
            'diterbitkan_oleh' => $admin->id,
            'diterbitkan_pada' => now(),
        ])->save();

        $alumni = Alumni::create(Alumni::isianDariPeserta($bayu->fresh()) + [
            'judul_project' => 'Dashboard monitoring audit internal',
            'deskripsi_project' => 'Membangun dashboard sederhana untuk memantau progres temuan audit per divisi.',
            'tampil' => true,
            'urutan' => 1,
        ]);
        app(FotoAlumni::class)->sinkron($alumni);

        // Galeri dokumentasi.
        $kegiatan = [
            ['Pembekalan peserta magang', 'Pengenalan perusahaan, tata tertib, dan pembagian divisi.', [38, 136, 180]],
            ['Kunjungan lapangan', 'Mendampingi tim konsultan ke lokasi klien.', [11, 106, 156]],
            ['Presentasi project akhir', 'Peserta mempresentasikan hasil project di depan mentor.', [238, 118, 35]],
            ['Pelatihan tools audit', 'Belajar memakai spreadsheet dan aplikasi audit.', [6, 118, 71]],
            ['Rapat mingguan', 'Evaluasi logbook dan target pekan depan.', [107, 114, 128]],
            ['Pelepasan alumni', 'Penyerahan surat keterangan magang.', [60, 90, 140]],
        ];

        foreach ($kegiatan as $i => [$judul, $keterangan, $warna]) {
            $dokumentasi = new Dokumentasi([
                'judul' => $judul,
                'keterangan' => $keterangan,
                'tanggal_kegiatan' => $hariIni->subDays(($i + 1) * 9)->toDateString(),
                'urutan' => $i + 1,
                'tampil' => true,
            ]);
            app(FotoDokumentasi::class)->simpan($dokumentasi, $this->gambar(1200, 800, $warna, $judul));
        }

        Model::reguard();
    }

    /**
     * Peserta + akun login + biodata lengkap dengan status tertentu.
     *
     * @param  array<string, mixed>  $data
     */
    private function peserta(string $nama, string $email, string $password, array $data, string $jenisKelamin, string $statusBiodata): PesertaMagang
    {
        $user = User::create([
            'name' => $nama,
            'email' => $email,
            'password' => $password,
            'role' => User::ROLE_PESERTA,
            'aktif' => true,
            'wajib_ganti_password' => false,
        ]);
        app(FotoProfil::class)->simpan($user, $this->gambar(400, 400, $jenisKelamin === 'perempuan' ? [238, 118, 35] : [38, 136, 180], mb_substr($nama, 0, 1)));

        $peserta = PesertaMagang::create($data + ['nama' => $nama, 'user_id' => $user->id]);

        BiodataPeserta::create([
            'peserta_magang_id' => $peserta->id,
            'nama_lengkap' => $nama,
            'nama_panggilan' => Str::before($nama, ' '),
            'jenis_kelamin' => $jenisKelamin,
            'tempat_lahir' => 'Yogyakarta',
            'tanggal_lahir' => '2004-05-12',
            'nik' => '3400000000000000',
            'alamat_ktp' => 'Jl. Contoh No. 1, Yogyakarta',
            'alamat_domisili' => 'Jl. Contoh No. 1, Yogyakarta',
            'no_hp' => '081200000000',
            'email' => $email,
            'perguruan_tinggi' => $data['asal_kampus'],
            'fakultas_jurusan' => $data['program_studi'],
            'nim' => $data['nim'],
            'semester' => '7',
            'ipk' => '3.62',
            'dosen_pembimbing_nama' => 'Dr. Contoh Pembimbing',
            'dosen_pembimbing_kontak' => '081300000000',
            'posisi_dilamar' => 'Staf Magang',
            'jenis_program' => 'wajib_kampus',
            'durasi_bulan' => 6,
            'keterampilan' => ['Microsoft Excel', 'Analisis data', 'Presentasi'],
            'bahasa' => ['Indonesia', 'Inggris'],
            'no_rekening' => '0000000000',
            'nama_bank' => 'Bank Contoh',
            'atas_nama_rekening' => $nama,
            'pernyataan_setuju' => true,
            'pernyataan_tempat' => 'Yogyakarta',
            'pernyataan_tanggal' => $data['periode_mulai'],
            'status' => $statusBiodata,
            'diajukan_pada' => now(),
        ]);

        return $peserta;
    }

    /**
     * Logbook hari kerja 4 minggu terakhir (satu periode penuh) dengan status beragam.
     */
    private function logbook(PesertaMagang $peserta, CarbonImmutable $hariIni, User $admin): void
    {
        $aktivitas = [
            'Merapikan berkas kertas kerja audit dan menginput ke spreadsheet.',
            'Mengikuti rapat koordinasi tim konsultan bersama mentor.',
            'Menyusun draft laporan ringkas temuan lapangan.',
            'Memeriksa kelengkapan dokumen klien sesuai checklist.',
        ];

        $hariKerja = collect(CarbonPeriod::create($hariIni->subDays(28), $hariIni->subDay()))
            ->map(fn ($tanggal) => CarbonImmutable::parse($tanggal))
            ->reject(fn (CarbonImmutable $tanggal) => $tanggal->isWeekend())
            ->values();

        foreach ($hariKerja as $i => $tanggal) {
            $baru = $i >= $hariKerja->count() - 3;

            $status = match (true) {
                $baru => LogbookHarian::MENUNGGU,
                $i === 4 => LogbookHarian::DITOLAK,
                default => LogbookHarian::DITERIMA,
            };

            LogbookHarian::create([
                'peserta_magang_id' => $peserta->id,
                'tanggal' => $tanggal->toDateString(),
                'kehadiran' => LogbookHarian::HADIR,
                'pagi_masuk' => '08:00',
                'pagi_keluar' => $i % 5 === 3 ? '12:00' : null,
                'siang_masuk' => null,
                'siang_keluar' => $i % 5 === 3 ? null : '16:30',
                'activity' => $aktivitas[$i % count($aktivitas)],
                'output' => 'Dokumen kerja hari ini selesai dan dikirim ke mentor.',
                'lokasi' => $i % 3 === 0 ? 'Kantor klien' : 'Kantor',
                'status_verifikasi' => $status,
                'catatan_verifikasi' => $status === LogbookHarian::DITOLAK ? 'Lengkapi output kegiatan dan jam pulang.' : null,
                'diverifikasi_oleh' => $status === LogbookHarian::MENUNGGU ? null : $admin->id,
                'diverifikasi_pada' => $status === LogbookHarian::MENUNGGU ? null : now(),
            ]);
        }
    }

    /**
     * Satu scan kartu yang belum disimpan (untuk pratinjau) dan dua logbook yang jamnya dari scan.
     */
    private function scanKartu(PesertaMagang $peserta, CarbonImmutable $hariIni): void
    {
        $bulan = $hariIni->subDays(10);
        $sisi = $bulan->day <= 15 ? ScanKartuAbsensi::SISI_AWAL : ScanKartuAbsensi::SISI_AKHIR;
        [$awal, $akhir] = ScanKartuAbsensi::rentangSisi($sisi, $bulan->year, $bulan->month);

        $hasil = collect(range($awal, min($akhir, $hariIni->subDay()->month === $bulan->month ? $hariIni->subDay()->day : $akhir)))
            ->filter(fn (int $hari) => ! $bulan->setDay($hari)->isWeekend())
            ->values()
            ->map(fn (int $hari, int $i) => [
                'tanggal' => $hari,
                'jam_masuk' => sprintf('07:%02d', 45 + ($i * 7) % 15),
                'jam_keluar' => $i % 6 === 5 ? null : sprintf('16:%02d', 30 + ($i * 11) % 25),
                'keyakinan' => $i % 6 === 5 ? 0.62 : 0.95,
                'ragu' => $i % 6 === 5,
                'catatan' => $i % 6 === 5 ? ['Cetakan jam keluar tidak terlihat.'] : [],
            ])
            ->all();

        $path = ScanKartuAbsensi::FOLDER.'/'.$peserta->id.'/contoh-kartu.jpg';
        Storage::disk('local')->put($path, file_get_contents($this->gambar(600, 800, [238, 118, 35], 'Contoh kartu')->getRealPath()));

        $scan = ScanKartuAbsensi::create([
            'peserta_magang_id' => $peserta->id,
            'tahun' => $bulan->year,
            'bulan' => $bulan->month,
            'sisi' => $sisi,
            'foto_path' => $path,
            'status' => ScanKartuAbsensi::DIPRATINJAU,
            'hasil' => $hasil,
            'jumlah_terbaca' => count($hasil),
            'model' => 'gemini-2.5-flash',
        ]);

        // Dua logbook terakhir di kartu ini jamnya dari scan; yang terbaru diubah peserta setelah scan.
        $bacaan = collect($hasil)->keyBy('tanggal');

        LogbookHarian::where('peserta_magang_id', $peserta->id)->where('kehadiran', LogbookHarian::HADIR)
            ->whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month)
            ->orderByDesc('tanggal')->limit(2)->get()
            ->filter(fn (LogbookHarian $logbook) => $bacaan->has($logbook->tanggal->day))
            ->each(function (LogbookHarian $logbook, int $i) use ($bacaan, $scan) {
                $asli = $bacaan[$logbook->tanggal->day];

                $logbook->update([
                    'pagi_masuk' => $i === 0 ? '08:00' : $asli['jam_masuk'],
                    'pagi_keluar' => null,
                    'siang_masuk' => null,
                    'siang_keluar' => $asli['jam_keluar'] ?? '16:30',
                    'sumber_jam' => LogbookHarian::SUMBER_SCAN,
                    'scan_kartu_absensi_id' => $scan->id,
                    'jam_scan_asli' => ['pagi_masuk' => $asli['jam_masuk'] ?? '', 'siang_keluar' => $asli['jam_keluar'] ?? ''],
                ]);
            });
    }

    private function penilaian(PesertaMagang $peserta, User $penilai): void
    {
        $details = AspekPenilaian::aktif()->with('kelompok')->whereNotNull('kelompok_penilaian_id')->get()
            ->map(fn (AspekPenilaian $aspek, int $i) => [
                'aspek_penilaian_id' => $aspek->id,
                'nama_aspek' => $aspek->nama,
                'kelompok' => $aspek->kelompok->nama,
                'bobot_kelompok' => $aspek->kelompok->bobot,
                'bobot' => 0,
                'nilai' => [88, 84, 86, 90, 82][$i % 5],
            ]);

        $nilaiAkhir = PenilaianPeserta::hitungNilaiAkhir($details);

        PenilaianPeserta::create([
            'peserta_magang_id' => $peserta->id,
            'nilai_akhir' => $nilaiAkhir,
            'predikat' => PenilaianPeserta::predikatUntuk($nilaiAkhir),
            'kekuatan' => 'Teliti, cepat belajar, dan komunikatif dengan tim.',
            'aspek_perbaikan' => 'Manajemen waktu saat menangani beberapa tugas sekaligus.',
            'langkah_perbaikan' => 'Membuat daftar prioritas harian dan melaporkan progres ke mentor.',
            'dinilai_oleh' => $penilai->id,
            'dinilai_pada' => now(),
        ])->details()->createMany($details->all());
    }

    /**
     * Gambar tanda tangan (coretan) atau stempel (lingkaran) contoh, PNG transparan.
     */
    private function tandaTangan(bool $stempel): UploadedFile
    {
        [$lebar, $tinggi] = $stempel ? [400, 400] : [600, 240];
        $gambar = imagecreatetruecolor($lebar, $tinggi);
        imagesavealpha($gambar, true);
        imagefill($gambar, 0, 0, (int) imagecolorallocatealpha($gambar, 0, 0, 0, 127));
        imagesetthickness($gambar, 6);

        if ($stempel) {
            $biru = (int) imagecolorallocatealpha($gambar, 30, 70, 160, 25);
            imageellipse($gambar, 200, 200, 380, 380, $biru);
            imageellipse($gambar, 200, 200, 300, 300, $biru);
            imagestring($gambar, 5, 148, 192, 'CONTOH PITSI', $biru);
        } else {
            $tinta = (int) imagecolorallocate($gambar, 25, 35, 80);
            $titik = [[40, 170], [110, 60], [170, 190], [240, 70], [300, 170], [380, 90], [450, 150], [560, 110]];
            foreach (array_slice($titik, 1) as $i => $ke) {
                imageline($gambar, $titik[$i][0], $titik[$i][1], $ke[0], $ke[1], $tinta);
            }
        }

        $path = tempnam(sys_get_temp_dir(), 'ttd').'.png';
        imagepng($gambar, $path);

        return new UploadedFile($path, 'contoh.png', 'image/png', null, true);
    }

    private function hariKerjaSebelum(CarbonImmutable $tanggal, int $jumlah): CarbonImmutable
    {
        while ($jumlah > 0) {
            $tanggal = $tanggal->subDay();

            if (! $tanggal->isWeekend()) {
                $jumlah--;
            }
        }

        return $tanggal;
    }

    /**
     * Gambar polos berwarna dengan teks, sebagai pengganti foto asli.
     *
     * @param  array{int, int, int}  $warna
     */
    private function gambar(int $lebar, int $tinggi, array $warna, string $teks): UploadedFile
    {
        $gambar = imagecreatetruecolor($lebar, $tinggi);
        imagefill($gambar, 0, 0, (int) imagecolorallocate($gambar, ...$warna));
        imagefilledellipse($gambar, (int) ($lebar * .72), (int) ($tinggi * .3), (int) ($tinggi * .9), (int) ($tinggi * .9), (int) imagecolorallocatealpha($gambar, 255, 255, 255, 100));

        $skala = max(1, (int) floor($lebar / 220));
        $kanvasTeks = imagecreatetruecolor(imagefontwidth(5) * mb_strlen($teks), imagefontheight(5));
        imagefill($kanvasTeks, 0, 0, (int) imagecolorallocate($kanvasTeks, ...$warna));
        imagestring($kanvasTeks, 5, 0, 0, $teks, (int) imagecolorallocate($kanvasTeks, 255, 255, 255));
        imagecopyresized($gambar, $kanvasTeks, (int) ($lebar * .06), (int) ($tinggi * .78), 0, 0, imagesx($kanvasTeks) * $skala, imagesy($kanvasTeks) * $skala, imagesx($kanvasTeks), imagesy($kanvasTeks));

        $path = tempnam(sys_get_temp_dir(), 'panduan').'.jpg';
        imagejpeg($gambar, $path, 88);

        return new UploadedFile($path, 'contoh.jpg', 'image/jpeg', null, true);
    }
}
