<?php

namespace Tests\Feature;

use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HalamanDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function buatPeserta(): PesertaMagang
    {
        return PesertaMagang::create([
            'nama' => 'Peserta Uji',
            'nim' => '2201999',
            'status' => 'aktif',
        ]);
    }

    /**
     * Halaman yang dapat dibuka Admin maupun Super Admin.
     *
     * @return array<string, array{string}>
     */
    public static function halamanBersama(): array
    {
        return [
            'profil perusahaan' => ['superadmin.perusahaan.index'],
            'kelola banner' => ['superadmin.banners.index'],
            'kelola konten' => ['superadmin.pengaturan.index'],
            'data peserta' => ['superadmin.peserta-magang.index'],
            'tambah peserta' => ['superadmin.peserta-magang.create'],
        ];
    }

    #[DataProvider('halamanBersama')]
    public function test_super_admin_dapat_membuka_halaman_bersama(string $routeName): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->get(route($routeName))
            ->assertOk()
            ->assertSee('Kelola Pengguna');
    }

    #[DataProvider('halamanBersama')]
    public function test_admin_dapat_membuka_halaman_bersama_tanpa_menu_pengguna(string $routeName): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route($routeName))
            ->assertOk()
            ->assertSee('Data Peserta')
            ->assertDontSee(route('superadmin.pengguna.index'));
    }

    public function test_dashboard_tampil_sesuai_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Admin');

        $this->actingAs($superAdmin)
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('Super Admin');
    }

    public function test_halaman_detail_dan_edit_peserta_dapat_dibuka(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $peserta = $this->buatPeserta();

        $this->actingAs($superAdmin)
            ->get(route('superadmin.peserta-magang.show', $peserta))
            ->assertOk()
            ->assertSee('Peserta Uji')
            ->assertSee('belum memiliki link Google Sheets');

        $this->actingAs($superAdmin)
            ->get(route('superadmin.peserta-magang.edit', $peserta))
            ->assertOk()
            ->assertSee('Peserta Uji');
    }

    public function test_halaman_kelola_pengguna_dapat_dibuka_super_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $pengguna = User::factory()->create(['role' => 'admin']);

        $this->actingAs($superAdmin)->get(route('superadmin.pengguna.index'))->assertOk();
        $this->actingAs($superAdmin)->get(route('superadmin.pengguna.create'))->assertOk();

        $this->actingAs($superAdmin)
            ->get(route('superadmin.pengguna.edit', $pengguna))
            ->assertOk()
            ->assertSee(route('superadmin.pengguna.update', $pengguna));
    }

    public function test_pesan_validasi_tampil_di_bawah_input(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->from(route('superadmin.pengguna.create'))
            ->post(route('superadmin.pengguna.store'), [])
            ->assertRedirect(route('superadmin.pengguna.create'));

        $this->actingAs($superAdmin)
            ->get(route('superadmin.pengguna.create'))
            ->assertSee('x-form-error', false)
            ->assertSee('x-alert-error', false);
    }
}
