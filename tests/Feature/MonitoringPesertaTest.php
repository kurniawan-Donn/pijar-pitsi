<?php

namespace Tests\Feature;

use App\Models\BiodataPeserta;
use App\Models\Divisi;
use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\PesertaMagang;
use App\Models\User;
use App\Services\MonitoringPeserta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Hari ini = Rabu, 12 Agustus 2026 (periode Agustus: 28 Jul – 27 Agt).
 */
class MonitoringPesertaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private PesertaMagang $rajin;

    private PesertaMagang $lupa;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-08-12 10:00:00');

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $periode = ['status' => 'aktif', 'periode_mulai' => '2026-06-03', 'periode_selesai' => '2026-11-30'];
        $this->rajin = PesertaMagang::create(['nama' => 'Ani Rajin', 'user_id' => User::factory()->create(['role' => 'peserta'])->id] + $periode);
        $this->lupa = PesertaMagang::create(['nama' => 'Budi Lupa'] + $periode);
        PesertaMagang::create(['nama' => 'Citra Selesai', 'status' => 'selesai']);

        $konsultan = Divisi::create(['nama' => 'Konsultan']);
        $this->rajin->penempatans()->create(['periode' => '2026-08', 'divisi_id' => $konsultan->id, 'mentor_nama' => 'Mentor A']);

        foreach (['2026-08-10', '2026-08-11', '2026-08-12'] as $tanggal) {
            LogbookHarian::create([
                'peserta_magang_id' => $this->rajin->id,
                'tanggal' => $tanggal,
                'kehadiran' => 'hadir',
                'pagi_masuk' => '08:00',
                'siang_keluar' => '16:30',
                'activity' => 'Kegiatan '.$tanggal,
                'status_verifikasi' => $tanggal === '2026-08-10' ? 'diterima' : 'menunggu',
            ]);
        }

        BiodataPeserta::create(['peserta_magang_id' => $this->rajin->id, 'nama_lengkap' => 'Ani Rajin', 'status' => 'diajukan']);
        IzinPeserta::create([
            'peserta_magang_id' => $this->lupa->id,
            'jenis' => 'izin',
            'tanggal_mulai' => '2026-08-11',
            'tanggal_selesai' => '2026-08-11',
            'keterangan' => 'Urusan kampus',
        ]);
    }

    public function test_ringkasan_peserta_aktif(): void
    {
        $ringkasan = app(MonitoringPeserta::class)->ringkasan();

        $this->assertSame(2, $ringkasan['peserta_aktif']);
        $this->assertSame(1, $ringkasan['punya_akun']);
        $this->assertSame(2, $ringkasan['wajib_hari_ini']);
        $this->assertSame(1, $ringkasan['sudah_isi_hari_ini']);
        $this->assertSame(2, $ringkasan['logbook_menunggu']);
        $this->assertSame(1, $ringkasan['izin_menunggu']);
        $this->assertSame(1, $ringkasan['biodata_diajukan']);
        $this->assertSame(1, $ringkasan['biodata_belum']);
    }

    public function test_tabel_menghitung_logbook_periode_berjalan(): void
    {
        $tabel = app(MonitoringPeserta::class)->tabel()->keyBy(fn (array $baris) => $baris['peserta']->nama);

        $rajin = $tabel['Ani Rajin'];
        $this->assertSame('Konsultan', $rajin['divisi']->nama);
        $this->assertSame(3, $rajin['terisi']);
        $this->assertSame(3, $rajin['rekap']['hari_penuh']);
        $this->assertSame(150000, $rajin['rekap']['uang_saku']);
        $this->assertTrue($rajin['isi_hari_ini']);
        $this->assertSame('2026-08-12', $rajin['terakhir']->toDateString());

        // 28 Jul – 12 Agt = 12 hari kerja, belum ada isian sama sekali.
        $this->assertSame(12, $tabel['Budi Lupa']['rekap']['belum_diisi']);
        $this->assertFalse($tabel['Budi Lupa']['isi_hari_ini']);
        $this->assertFalse($tabel->has('Citra Selesai'));
    }

    public function test_filter_dan_urutan(): void
    {
        $monitoring = app(MonitoringPeserta::class);

        $this->assertSame(['Budi Lupa'], $monitoring->tabel(['belum_hari_ini' => true])->pluck('peserta.nama')->all());
        $this->assertSame(['Ani Rajin'], $monitoring->tabel(['divisi' => Divisi::first()->id])->pluck('peserta.nama')->all());
        $this->assertSame(['Citra Selesai'], $monitoring->tabel(['status' => 'selesai'])->pluck('peserta.nama')->all());
        $this->assertSame(['Budi Lupa'], $monitoring->tabel(['cari' => 'budi'])->pluck('peserta.nama')->all());
        $this->assertSame('Budi Lupa', $monitoring->tabel(['urut' => 'belum_diisi'])->first()['peserta']->nama);
        $this->assertSame('Ani Rajin', $monitoring->tabel(['urut' => 'menunggu'])->first()['peserta']->nama);
    }

    public function test_hari_libur_tidak_wajib_isi(): void
    {
        Carbon::setTestNow('2026-08-15 10:00:00'); // Sabtu

        $ringkasan = app(MonitoringPeserta::class)->ringkasan();

        $this->assertSame(0, $ringkasan['wajib_hari_ini']);
    }

    public function test_halaman_monitoring(): void
    {
        $this->actingAs($this->admin)
            ->get(route('superadmin.monitoring.index'))
            ->assertOk()
            ->assertSee('Ani Rajin')
            ->assertSee('Budi Lupa')
            ->assertDontSee('Citra Selesai')
            ->assertSee('Hari ini belum')
            ->assertSee('Mentor: Mentor A')
            ->assertSee('Kegiatan 2026-08-12')
            ->assertSee(route('superadmin.peserta-magang.logbook-harian', $this->lupa));

        $this->actingAs($this->admin)
            ->get(route('superadmin.monitoring.index', ['belum_hari_ini' => 1]))
            ->assertOk()
            ->assertSee('Budi Lupa')
            ->assertDontSee('>Hari ini</span>', false);
    }

    public function test_dashboard_menampilkan_ringkasan_monitoring(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Logbook Hari Ini')
            ->assertSee('1/2')
            ->assertSee(route('superadmin.monitoring.index', ['belum_hari_ini' => 1]), false);

        $this->actingAs(User::factory()->create(['role' => 'super_admin']))
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('Izin / Sakit Menunggu');
    }

    public function test_peserta_tidak_bisa_membuka_monitoring(): void
    {
        $this->actingAs($this->rajin->user)->get(route('superadmin.monitoring.index'))->assertForbidden();
    }
}
