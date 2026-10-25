<?php

namespace Tests\Feature;

use App\Models\LogbookHarian;
use App\Models\Mahasiswa;
use App\Models\PengajuanMagang;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use App\Models\User;
use App\Services\GoogleSheetsService;
use App\Services\MonitoringPeserta;
use App\Services\StatistikMagang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;
use Tests\TestCase;

class StatistikDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 09:00:00');
    }

    private function peserta(array $data): PesertaMagang
    {
        return PesertaMagang::create(array_merge(['nama' => fake()->name(), 'status' => 'aktif'], $data));
    }

    public function test_jumlah_peserta_dan_yang_belum_dinilai(): void
    {
        $this->peserta(['status' => 'aktif']);
        $this->peserta(['status' => 'aktif']);
        $sudahDinilai = $this->peserta(['status' => 'selesai']);
        $this->peserta(['status' => 'selesai']);

        PenilaianPeserta::create(['peserta_magang_id' => $sudahDinilai->id, 'nilai_akhir' => 80, 'predikat' => 'B']);

        $this->assertSame(
            ['aktif' => 2, 'selesai' => 2, 'total' => 4, 'belum_dinilai' => 1],
            app(StatistikMagang::class)->peserta()
        );
    }

    public function test_pengajuan_per_status_dan_per_bulan(): void
    {
        PengajuanMagang::factory()->count(2)->create();
        PengajuanMagang::factory()->disetujui()->create();
        PengajuanMagang::factory()->create(['created_at' => '2026-08-10 10:00:00']);
        PengajuanMagang::factory()->create(['created_at' => '2026-01-10 10:00:00']); // di luar 6 bulan

        $statistik = app(StatistikMagang::class);

        $this->assertSame(['menunggu' => 4, 'disetujui' => 1, 'ditolak' => 0], $statistik->pengajuanPerStatus());

        $perBulan = collect($statistik->pengajuanPerBulan())->pluck('jumlah', 'bulan')->all();
        $this->assertSame(
            ['2026-05' => 0, '2026-06' => 0, '2026-07' => 0, '2026-08' => 1, '2026-09' => 0, '2026-10' => 3],
            $perBulan
        );
    }

    public function test_peserta_berakhir_bulan_ini(): void
    {
        $this->peserta(['nama' => 'Selesai Oktober', 'periode_selesai' => '2026-10-31']);
        $this->peserta(['nama' => 'Selesai November', 'periode_selesai' => '2026-11-05']);
        $this->peserta(['nama' => 'Sudah Selesai', 'status' => 'selesai', 'periode_selesai' => '2026-10-02']);

        $this->assertSame(
            ['Selesai Oktober'],
            app(StatistikMagang::class)->berakhirBulanIni()->pluck('nama')->all()
        );
    }

    public function test_kehadiran_dashboard_dari_logbook_tanpa_google_sheets(): void
    {
        $peserta = $this->peserta([
            'google_sheet_url' => 'https://docs.google.com/spreadsheets/d/abc/edit',
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
        ]);

        LogbookHarian::create([
            'peserta_magang_id' => $peserta->id,
            'tanggal' => now()->toDateString(),
            'kehadiran' => 'hadir',
            'pagi_masuk' => '08:00',
            'siang_keluar' => '16:30',
            'activity' => 'Kegiatan',
        ]);

        // Dashboard tidak boleh memanggil Google sama sekali.
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('getSheetTitlesFromUrl');
            $mock->shouldNotReceive('getValuesFromUrl');
        });

        $monitoring = app(MonitoringPeserta::class);
        $kehadiran = $monitoring->kehadiranPeriode($monitoring->tabel());

        $this->assertSame(1, $kehadiran['peserta']);
        $this->assertSame(1, $kehadiran['hadir']);
        $this->assertSame(50000, $kehadiran['uang_saku']);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Kehadiran Periode '.$kehadiran['periode']);
    }

    public function test_dashboard_admin_dan_super_admin_menampilkan_statistik(): void
    {
        $mahasiswa = Mahasiswa::factory()->create(['nama' => 'Pendaftar Terbaru']);
        PengajuanMagang::factory()->for($mahasiswa)->create();
        $this->peserta(['nama' => 'Akan Selesai', 'periode_selesai' => '2026-10-20']);

        foreach (['admin' => 'admin.dashboard', 'super_admin' => 'superadmin.dashboard'] as $role => $route) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route($route))
                ->assertOk()
                ->assertSee('Pengajuan Menunggu')
                ->assertSee('Pendaftar Terbaru')
                ->assertSee('Akan Selesai')
                ->assertSee('Belum dinilai');
        }
    }
}
