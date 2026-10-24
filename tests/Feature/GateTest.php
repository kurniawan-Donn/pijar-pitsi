<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * [gate => route yang dijaganya]
     *
     * @return array<string, array{string, array<int, string>}>
     */
    public static function gateDanRoute(): array
    {
        return [
            'kelola-pengguna' => ['kelola-pengguna', [
                'superadmin.pengguna.index',
                'superadmin.pengguna.create',
                'superadmin.pengguna.store',
                'superadmin.pengguna.edit',
                'superadmin.pengguna.update',
                'superadmin.pengguna.destroy',
            ]],
            'kelola-aspek-penilaian' => ['kelola-aspek-penilaian', [
                'superadmin.aspek-penilaian.index',
                'superadmin.aspek-penilaian.update',
                'superadmin.aspek-penilaian.predikat',
            ]],
            'lihat-log-aktivitas' => ['lihat-log-aktivitas', [
                'superadmin.log-aktivitas.index',
            ]],
        ];
    }

    /**
     * @param  array<int, string>  $routes
     */
    #[DataProvider('gateDanRoute')]
    public function test_gate_hanya_mengizinkan_super_admin(string $gate, array $routes): void
    {
        $this->assertTrue(Gate::forUser(User::factory()->make(['role' => 'super_admin']))->allows($gate));
        $this->assertFalse(Gate::forUser(User::factory()->make(['role' => 'admin']))->allows($gate));
    }

    /**
     * @param  array<int, string>  $routes
     */
    #[DataProvider('gateDanRoute')]
    public function test_route_khusus_super_admin_dijaga_gate(string $gate, array $routes): void
    {
        foreach ($routes as $nama) {
            $this->assertContains(
                "can:{$gate}",
                Route::getRoutes()->getByName($nama)->gatherMiddleware(),
                "Route {$nama} belum dijaga Gate {$gate}"
            );
        }
    }

    public function test_menu_sidebar_mengikuti_gate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $halamanAdmin = $this->actingAs($admin)->get(route('admin.dashboard'));
        $halamanSuperAdmin = $this->actingAs($superAdmin)->get(route('superadmin.dashboard'));

        foreach (['superadmin.pengguna.index', 'superadmin.aspek-penilaian.index', 'superadmin.log-aktivitas.index'] as $nama) {
            $halamanAdmin->assertDontSee(route($nama));
            $halamanSuperAdmin->assertSee(route($nama));
        }
    }
}
