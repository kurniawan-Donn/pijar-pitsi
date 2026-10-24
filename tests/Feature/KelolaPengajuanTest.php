<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\PengajuanMagang;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KelolaPengajuanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function rolePengelola(): array
    {
        return [
            'admin' => ['admin'],
            'super admin' => ['super_admin'],
        ];
    }

    private function pengelola(string $role = 'admin'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    #[DataProvider('rolePengelola')]
    public function test_admin_dan_super_admin_dapat_melihat_daftar_pengajuan(string $role): void
    {
        $pengajuan = PengajuanMagang::factory()->create();

        $this->actingAs($this->pengelola($role))
            ->get(route('admin.pengajuan.index'))
            ->assertOk()
            ->assertSee($pengajuan->nomor())
            ->assertSee($pengajuan->mahasiswa->nama);
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $pengajuan = PengajuanMagang::factory()->create();

        $this->get(route('admin.pengajuan.index'))->assertRedirect(route('login'));
        $this->get(route('admin.pengajuan.show', $pengajuan))->assertRedirect(route('login'));
        $this->put(route('admin.pengajuan.status', $pengajuan), ['status' => 'disetujui'])->assertRedirect(route('login'));
        $this->delete(route('admin.pengajuan.destroy', $pengajuan))->assertRedirect(route('login'));

        $this->assertSame(PengajuanMagang::STATUS_MENUNGGU, $pengajuan->fresh()->status);
    }

    public function test_daftar_dapat_difilter_status_dan_dicari(): void
    {
        $budi = Mahasiswa::factory()->create(['nama' => 'Budi Santoso', 'nim' => '111']);
        $sari = Mahasiswa::factory()->create(['nama' => 'Sari Dewi', 'nim' => '222']);

        PengajuanMagang::factory()->for($budi)->create();
        PengajuanMagang::factory()->disetujui()->for($sari)->create();

        $admin = $this->pengelola();

        $this->actingAs($admin)
            ->get(route('admin.pengajuan.index', ['status' => 'disetujui']))
            ->assertSee('Sari Dewi')
            ->assertDontSee('Budi Santoso');

        $this->actingAs($admin)
            ->get(route('admin.pengajuan.index', ['cari' => '111']))
            ->assertSee('Budi Santoso')
            ->assertDontSee('Sari Dewi');
    }

    public function test_detail_pengajuan_dapat_dibuka(): void
    {
        $pengajuan = PengajuanMagang::factory()->create(['posisi' => 'Desain Grafis']);

        $this->actingAs($this->pengelola())
            ->get(route('admin.pengajuan.show', $pengajuan))
            ->assertOk()
            ->assertSee('Desain Grafis')
            ->assertSee($pengajuan->mahasiswa->email);
    }

    public function test_ubah_status_menyimpan_catatan_tanpa_menimpa_keterangan_pendaftar(): void
    {
        $pengajuan = PengajuanMagang::factory()->create(['keterangan' => 'Motivasi pendaftar']);

        $this->actingAs($this->pengelola())
            ->put(route('admin.pengajuan.status', $pengajuan), [
                'status' => 'disetujui',
                'catatan_admin' => 'Datang hari Senin.',
            ])
            ->assertRedirect(route('admin.pengajuan.show', $pengajuan))
            ->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertSame('disetujui', $pengajuan->status);
        $this->assertSame('Datang hari Senin.', $pengajuan->catatan_admin);
        $this->assertSame('Motivasi pendaftar', $pengajuan->keterangan);
    }

    public function test_menolak_pengajuan_wajib_mengisi_catatan(): void
    {
        $pengajuan = PengajuanMagang::factory()->create();

        $this->actingAs($this->pengelola())
            ->put(route('admin.pengajuan.status', $pengajuan), ['status' => 'ditolak'])
            ->assertSessionHasErrors('catatan_admin');

        $this->assertSame(PengajuanMagang::STATUS_MENUNGGU, $pengajuan->fresh()->status);
    }

    public function test_status_tidak_dikenal_ditolak(): void
    {
        $pengajuan = PengajuanMagang::factory()->create();

        $this->actingAs($this->pengelola())
            ->put(route('admin.pengajuan.status', $pengajuan), ['status' => 'lulus'])
            ->assertSessionHasErrors('status');
    }

    public function test_hapus_pengajuan_juga_menghapus_dokumen(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('pengajuan/surat.pdf', 'isi');

        $pengajuan = PengajuanMagang::factory()->denganDokumen('pengajuan/surat.pdf')->create();

        $this->actingAs($this->pengelola())
            ->delete(route('admin.pengajuan.destroy', $pengajuan))
            ->assertRedirect(route('admin.pengajuan.index'));

        $this->assertModelMissing($pengajuan);
        $this->assertModelExists($pengajuan->mahasiswa);
        Storage::disk('local')->assertMissing('pengajuan/surat.pdf');
    }

    public function test_dokumen_dapat_diunduh_pengelola(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('pengajuan/surat.pdf', '%PDF-uji');

        $pengajuan = PengajuanMagang::factory()->denganDokumen('pengajuan/surat.pdf')->create();

        $this->actingAs($this->pengelola())
            ->get(route('admin.pengajuan.dokumen', $pengajuan))
            ->assertOk();

        $this->get(route('admin.pengajuan.dokumen', PengajuanMagang::factory()->create()))
            ->assertNotFound();
    }

    public function test_buat_peserta_dari_pengajuan_disetujui(): void
    {
        $mahasiswa = Mahasiswa::factory()->create([
            'nama' => 'Rina Pratiwi',
            'nim' => '2201000001',
            'asal_kampus' => 'Universitas Contoh',
            'program_studi' => 'Sistem Informasi',
        ]);
        $pengajuan = PengajuanMagang::factory()->disetujui()->for($mahasiswa)->create();

        $response = $this->actingAs($this->pengelola())
            ->post(route('admin.pengajuan.peserta', $pengajuan));

        $peserta = PesertaMagang::firstWhere('pengajuan_magang_id', $pengajuan->id);

        $this->assertNotNull($peserta);
        $response->assertRedirect(route('superadmin.peserta-magang.edit', $peserta));
        $this->assertSame('Rina Pratiwi', $peserta->nama);
        $this->assertSame('Universitas Contoh', $peserta->asal_kampus);
        $this->assertTrue($peserta->periode_mulai->equalTo($pengajuan->tanggal_mulai));
        $this->assertSame('aktif', $peserta->status);
    }

    public function test_peserta_tidak_dibuat_dua_kali_dan_tidak_dari_pengajuan_menunggu(): void
    {
        $admin = $this->pengelola();

        $menunggu = PengajuanMagang::factory()->create();
        $this->actingAs($admin)->post(route('admin.pengajuan.peserta', $menunggu))->assertSessionHas('error');

        $disetujui = PengajuanMagang::factory()->disetujui()->create();
        $this->actingAs($admin)->post(route('admin.pengajuan.peserta', $disetujui));
        $this->actingAs($admin)->post(route('admin.pengajuan.peserta', $disetujui))->assertSessionHas('error');

        $this->assertSame(1, PesertaMagang::count());
    }

    public function test_sidebar_menampilkan_jumlah_pengajuan_menunggu(): void
    {
        PengajuanMagang::factory()->count(3)->create();
        PengajuanMagang::factory()->disetujui()->create();

        $this->actingAs($this->pengelola('super_admin'))
            ->get(route('superadmin.dashboard'))
            ->assertSee('Pengajuan Magang')
            ->assertSee('<span class="dash-sidebar-badge" title="Pengajuan menunggu diproses">3</span>', false);
    }
}
