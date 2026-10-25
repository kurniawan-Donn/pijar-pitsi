<?php

namespace Tests\Feature;

use App\Models\BiodataPeserta;
use App\Models\FeedbackPeserta;
use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use App\Models\SuratKeterangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Halaman daftar per fitur di sidebar admin (Biodata, Logbook & Absensi,
 * Penilaian, Surat Keterangan), Rekap Kehadiran peserta, dan sidebar baru.
 * Hari ini = Rabu, 12 Agustus 2026 (periode Agustus: 28 Juli – 27 Agustus).
 */
class DaftarPesertaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-08-12 10:00:00');

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function peserta(string $nama, string $status = 'aktif', array $data = []): PesertaMagang
    {
        return PesertaMagang::create($data + [
            'nama' => $nama,
            'status' => $status,
            'periode_mulai' => '2026-06-01',
            'periode_selesai' => '2026-11-30',
        ]);
    }

    private function biodata(PesertaMagang $peserta, string $status): BiodataPeserta
    {
        return BiodataPeserta::create([
            'peserta_magang_id' => $peserta->id,
            'nama_lengkap' => $peserta->nama,
            'status' => $status,
        ]);
    }

    private function logbook(PesertaMagang $peserta, string $tanggal, string $status = LogbookHarian::MENUNGGU): LogbookHarian
    {
        return LogbookHarian::create([
            'peserta_magang_id' => $peserta->id,
            'tanggal' => $tanggal,
            'kehadiran' => 'hadir',
            'pagi_masuk' => '08:00',
            'siang_keluar' => '16:30',
            'activity' => 'Audit',
            'status_verifikasi' => $status,
        ]);
    }

    public function test_daftar_biodata_menampilkan_status_dan_menyaring_yang_perlu_ditinjau(): void
    {
        $diajukan = $this->peserta('Ani Diajukan');
        $this->biodata($diajukan, BiodataPeserta::STATUS_DIAJUKAN);
        $disetujui = $this->peserta('Budi Disetujui');
        $this->biodata($disetujui, BiodataPeserta::STATUS_DISETUJUI);
        $this->peserta('Cici Belum');

        $this->actingAs($this->admin)
            ->get(route('superadmin.biodata.index'))
            ->assertOk()
            ->assertSeeInOrder(['Ani Diajukan', 'Cici Belum', 'Budi Disetujui'])
            ->assertSee('Menunggu tinjauan (1)')
            ->assertSee(route('superadmin.peserta-magang.biodata', $diajukan));

        $this->actingAs($this->admin)
            ->get(route('superadmin.biodata.index', ['keadaan' => 'diajukan']))
            ->assertSee('Ani Diajukan')
            ->assertDontSee('Budi Disetujui')
            ->assertDontSee('Cici Belum');
    }

    public function test_daftar_logbook_menyaring_yang_menunggu_verifikasi(): void
    {
        $menunggu = $this->peserta('Dodi Menunggu');
        $this->logbook($menunggu, '2026-08-11');
        $beres = $this->peserta('Eka Beres');
        $this->logbook($beres, '2026-08-11', LogbookHarian::DITERIMA);
        $izin = $this->peserta('Fajar Izin');
        IzinPeserta::create([
            'peserta_magang_id' => $izin->id,
            'jenis' => 'izin',
            'tanggal_mulai' => '2026-08-13',
            'tanggal_selesai' => '2026-08-13',
            'keterangan' => 'Kampus',
            'status' => LogbookHarian::MENUNGGU,
        ]);

        $this->actingAs($this->admin)
            ->get(route('superadmin.logbook.index'))
            ->assertOk()
            ->assertSee(['Dodi Menunggu', 'Eka Beres', 'Fajar Izin'])
            ->assertSee('1 izin')
            ->assertSee(route('superadmin.peserta-magang.logbook-harian', $menunggu));

        $this->actingAs($this->admin)
            ->get(route('superadmin.logbook.index', ['keadaan' => 'menunggu']))
            ->assertSee('Dodi Menunggu')
            ->assertSee('Fajar Izin')
            ->assertDontSee('Eka Beres');
    }

    public function test_daftar_penilaian_memisahkan_yang_belum_dinilai(): void
    {
        $dinilai = $this->peserta('Gita Dinilai', 'selesai');
        PenilaianPeserta::create(['peserta_magang_id' => $dinilai->id, 'nilai_akhir' => 86.4, 'predikat' => 'Sangat Memuaskan']);
        $this->peserta('Hadi Belum', 'aktif');

        $this->actingAs($this->admin)
            ->get(route('superadmin.penilaian.index'))
            ->assertOk()
            ->assertSeeInOrder(['Hadi Belum', 'Gita Dinilai'])
            ->assertSee('86,40')
            ->assertSee('Sangat Memuaskan');

        $this->actingAs($this->admin)
            ->get(route('superadmin.penilaian.index', ['keadaan' => 'belum']))
            ->assertSee('Hadi Belum')
            ->assertDontSee('Gita Dinilai');
    }

    public function test_daftar_surat_menunjukkan_syarat_dan_yang_siap_terbit(): void
    {
        $siap = $this->peserta('Indah Siap', 'selesai');
        $this->biodata($siap, BiodataPeserta::STATUS_DISETUJUI);
        PenilaianPeserta::create(['peserta_magang_id' => $siap->id, 'nilai_akhir' => 80, 'predikat' => 'Memuaskan']);
        FeedbackPeserta::create([
            'peserta_magang_id' => $siap->id,
            'skor' => array_fill_keys(range(1, 10), 5),
            'paling_disukai' => 'a', 'kendala' => 'b', 'perlu_diperbaiki' => 'c', 'saran' => 'd',
            'rekomendasi' => 'bersedia', 'evaluasi_keseluruhan' => 'e', 'pernyataan' => true, 'jumlah_kirim' => 1,
        ]);
        $this->peserta('Joko Kurang', 'selesai');
        $this->peserta('Kiki Aktif', 'aktif');

        $this->actingAs($this->admin)
            ->get(route('superadmin.surat.index'))
            ->assertOk()
            ->assertSeeInOrder(['Indah Siap', 'Joko Kurang'])
            ->assertSee('>Lengkap</span>', false)
            ->assertSee('Biodata belum disetujui.')
            // Bawaan hanya peserta selesai.
            ->assertDontSee('Kiki Aktif');

        SuratKeterangan::where('peserta_magang_id', $siap->id)->update(['status' => SuratKeterangan::TERBIT, 'nomor_surat' => '001/SKM/VIII/2026']);

        $this->actingAs($this->admin)
            ->get(route('superadmin.surat.index', ['keadaan' => 'terbit']))
            ->assertSee('Indah Siap')
            ->assertSee('001/SKM/VIII/2026')
            ->assertDontSee('Joko Kurang');
    }

    public function test_filter_keadaan_tidak_dikenal_ditolak(): void
    {
        $this->actingAs($this->admin)
            ->get(route('superadmin.biodata.index', ['keadaan' => 'apa-saja']))
            ->assertSessionHasErrors('keadaan');
    }

    public function test_rekap_kehadiran_peserta_per_periode_dan_total(): void
    {
        $akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $peserta = $this->peserta('Lina', 'aktif', ['user_id' => $akun->id]);
        $this->logbook($peserta, '2026-07-01', LogbookHarian::DITERIMA);
        $this->logbook($peserta, '2026-08-11');

        $this->actingAs($akun)
            ->get(route('peserta.rekap'))
            ->assertOk()
            ->assertSee('Agustus 2026 · 28 Juli s.d. 27 Agustus 2026')
            ->assertSee('Rp 100.000')
            ->assertSee('Semua Periode')
            ->assertSeeInOrder(['Juni', 'Juli', 'Agustus', 'Total'])
            // Periode yang belum dimulai tidak ikut ringkasan.
            ->assertDontSee(route('peserta.rekap', ['periode' => '2026-10']));

        $this->actingAs($akun)
            ->get(route('peserta.rekap', ['periode' => '2026-07']))
            ->assertOk()
            ->assertSee(route('peserta.logbook.cetak', ['periode' => '2026-07']), false);
    }

    public function test_sidebar_admin_dikelompokkan_dan_menu_sistem_khusus_super_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(route('superadmin.monitoring.index'))
            ->assertSeeInOrder(['Utama', 'Kelola Peserta', 'Laporan', 'Konten Website'])
            ->assertSee(['Logbook &amp; Absensi', 'Rekap Uang Saku', 'Hari Libur'], false)
            ->assertDontSee('Kelola Pengguna')
            ->assertDontSee('Log Aktivitas')
            ->assertDontSee('>Sistem<', false);

        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($superAdmin)
            ->get(route('superadmin.monitoring.index'))
            ->assertSeeInOrder(['Kelola Peserta', 'Laporan', 'Konten Website', 'Sistem', 'Kelola Pengguna', 'Aspek Penilaian', 'Pengaturan', 'Log Aktivitas'])
            ->assertDontSee('Hari Libur');
    }

    public function test_sidebar_peserta_dikelompokkan(): void
    {
        $akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->peserta('Mira', 'aktif', ['user_id' => $akun->id]);

        $this->actingAs($akun)
            ->get(route('peserta.dashboard'))
            ->assertSeeInOrder(['Utama', 'Data Saya', 'Biodata', 'Kegiatan', 'Logbook Harian', 'Ajukan Izin / Sakit', 'Rekap Kehadiran', 'Akhir Magang', 'Feedback', 'Surat Keterangan', 'Akun', 'Profil'])
            ->assertSee(route('peserta.rekap'));
    }
}
