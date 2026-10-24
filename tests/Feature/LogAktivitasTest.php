<?php

namespace Tests\Feature;

use App\Models\LogAktivitas;
use App\Models\PengajuanMagang;
use App\Models\PesertaMagang;
use App\Models\User;
use App\Services\GoogleSheetsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;
use Tests\TestCase;

class LogAktivitasTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 09:00:00');

        $this->admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Pencatat']);
    }

    public function test_ubah_status_pengajuan_tercatat_dengan_status_lama_dan_baru(): void
    {
        $pengajuan = PengajuanMagang::factory()->create();

        $this->actingAs($this->admin)->put(route('admin.pengajuan.status', $pengajuan), [
            'status' => 'ditolak',
            'catatan_admin' => 'Kuota penuh',
        ]);

        $log = LogAktivitas::firstWhere('aksi', 'ubah_status_pengajuan');

        $this->assertNotNull($log);
        $this->assertSame($this->admin->id, $log->user_id);
        $this->assertSame('Admin Pencatat', $log->nama_pengguna);
        $this->assertTrue($log->subjek->is($pengajuan));
        $this->assertSame(['dari' => 'menunggu', 'ke' => 'ditolak', 'catatan_admin' => 'Kuota penuh'], $log->data);
    }

    public function test_hapus_pengajuan_dan_peserta_tercatat(): void
    {
        $pengajuan = PengajuanMagang::factory()->create();
        $peserta = PesertaMagang::create(['nama' => 'Peserta Dihapus', 'nim' => '123', 'status' => 'aktif']);

        $this->actingAs($this->admin)->delete(route('admin.pengajuan.destroy', $pengajuan));
        $this->actingAs($this->admin)->delete(route('superadmin.peserta-magang.destroy', $peserta));

        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'hapus_pengajuan', 'subjek_id' => $pengajuan->id]);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'hapus_peserta', 'keterangan' => 'Peserta Peserta Dihapus dihapus']);
    }

    public function test_ubah_absensi_tercatat(): void
    {
        $peserta = PesertaMagang::create([
            'nama' => 'Peserta Absen',
            'status' => 'aktif',
            'google_sheet_url' => 'https://docs.google.com/spreadsheets/d/abc/edit',
        ]);

        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andReturn(['OKTOBER 2026']);
            $mock->shouldReceive('updateValuesFromUrl')->andReturn(4);
        });

        $this->actingAs($this->admin)->put(route('superadmin.peserta-magang.absensi.update', $peserta), [
            'bulan' => '2026-10',
            'sheet_row' => 15,
            'pagi_masuk' => '08:00',
        ]);

        $log = LogAktivitas::firstWhere('aksi', 'ubah_absensi');

        $this->assertNotNull($log);
        $this->assertSame('08:00', $log->data['pagi_masuk']);
        $this->assertSame(15, (int) $log->data['sheet_row']);
    }

    public function test_absensi_gagal_tidak_tercatat(): void
    {
        $peserta = PesertaMagang::create([
            'nama' => 'Peserta Absen',
            'status' => 'aktif',
            'google_sheet_url' => 'https://docs.google.com/spreadsheets/d/abc/edit',
        ]);

        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andThrow(new \RuntimeException('403'));
        });

        $this->actingAs($this->admin)->put(route('superadmin.peserta-magang.absensi.update', $peserta), [
            'bulan' => '2026-10',
            'sheet_row' => 15,
        ]);

        $this->assertDatabaseMissing('log_aktivitas', ['aksi' => 'ubah_absensi']);
    }

    public function test_halaman_log_khusus_super_admin_dan_bisa_difilter(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($this->admin);
        LogAktivitas::catat('hapus_peserta', null, 'Peserta A dihapus');
        LogAktivitas::catat('ubah_absensi', null, 'Absensi B diperbarui');

        $this->actingAs($this->admin)->get(route('superadmin.log-aktivitas.index'))->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('superadmin.log-aktivitas.index', ['aksi' => 'hapus_peserta']))
            ->assertOk()
            ->assertSee('Peserta A dihapus')
            ->assertDontSee('Absensi B diperbarui')
            ->assertSee('Admin Pencatat');
    }

    public function test_log_tetap_terbaca_setelah_akun_pelaku_dihapus(): void
    {
        $this->actingAs($this->admin);
        $log = LogAktivitas::catat('hapus_peserta', null, 'Peserta C dihapus');

        $this->admin->delete();

        $log->refresh();
        $this->assertNull($log->user_id);
        $this->assertSame('Admin Pencatat', $log->nama_pengguna);
    }
}
