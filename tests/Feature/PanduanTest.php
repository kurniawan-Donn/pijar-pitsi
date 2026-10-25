<?php

namespace Tests\Feature;

use App\Models\PesertaMagang;
use App\Models\User;
use App\Services\BukuPanduan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Menu Panduan: isi dari docs/panduan, bagian sesuai role yang login.
 */
class PanduanTest extends TestCase
{
    use RefreshDatabase;

    private function akun(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);

        if ($role === User::ROLE_PESERTA) {
            PesertaMagang::create([
                'nama' => 'Peserta Panduan',
                'status' => 'aktif',
                'periode_mulai' => now()->subMonth()->toDateString(),
                'periode_selesai' => now()->addMonths(2)->toDateString(),
                'user_id' => $user->id,
            ]);
        }

        return $user;
    }

    /**
     * @return array<string, array{string, string, array<int, string>, array<int, string>}>
     */
    public static function peranDanBagian(): array
    {
        return [
            'peserta' => [User::ROLE_PESERTA, 'Panduan Peserta Magang', ['peserta', 'alur'], ['admin', 'super-admin']],
            'admin' => [User::ROLE_ADMIN, 'Panduan Admin', ['admin', 'alur'], ['peserta', 'super-admin']],
            'super admin' => [User::ROLE_SUPER_ADMIN, 'Panduan Super Admin', ['super-admin', 'admin', 'alur'], ['peserta']],
        ];
    }

    /**
     * @param  array<int, string>  $boleh
     * @param  array<int, string>  $tidakBoleh
     */
    #[DataProvider('peranDanBagian')]
    public function test_panduan_sesuai_role(string $role, string $judulBawaan, array $boleh, array $tidakBoleh): void
    {
        $user = $this->akun($role);

        $this->actingAs($user)
            ->get(route('panduan'))
            ->assertOk()
            ->assertSee($judulBawaan)
            ->assertSee('Daftar Isi');

        foreach ($boleh as $bagian) {
            $this->actingAs($user)->get(route('panduan', $bagian))->assertOk();
        }

        foreach ($tidakBoleh as $bagian) {
            $this->actingAs($user)->get(route('panduan', $bagian))->assertNotFound();
        }

        $this->actingAs($user)->get('/panduan/tidak-ada')->assertNotFound();
    }

    public function test_gambar_diarahkan_ke_public_dan_semua_file_gambar_ada(): void
    {
        $this->actingAs($this->akun(User::ROLE_PESERTA))
            ->get(route('panduan', 'peserta'))
            ->assertSee('src="'.asset('images/panduan/peserta-dashboard.jpg').'"', false)
            ->assertDontSee('../../public/', false);

        foreach (BukuPanduan::BAGIAN as [, $file]) {
            preg_match_all('#\.\./\.\./public/(images/panduan/[^)\s]+)#', file_get_contents(base_path('docs/panduan/'.$file)), $gambar);

            foreach ($gambar[1] as $path) {
                $this->assertFileExists(public_path($path), "Gambar {$path} di {$file} belum ada.");
            }
        }
    }

    public function test_alur_menampilkan_diagram_html_dan_tautan_hanya_ke_bagian_yang_boleh(): void
    {
        $this->actingAs($this->akun(User::ROLE_PESERTA))
            ->get(route('panduan', 'alur'))
            ->assertOk()
            ->assertSee('class="diagram-alur"', false)
            ->assertDontSee('language-mermaid', false)
            ->assertSee('href="'.route('panduan', 'peserta').'"', false)
            ->assertDontSee('href="'.route('panduan', 'admin').'"', false)
            ->assertSee('Panduan Admin');
    }

    public function test_menu_panduan_di_sidebar(): void
    {
        $this->actingAs($this->akun(User::ROLE_ADMIN))
            ->get(route('admin.dashboard'))
            ->assertSeeInOrder(['Bantuan', 'Panduan']);

        $this->actingAs($this->akun(User::ROLE_PESERTA))
            ->get(route('peserta.dashboard'))
            ->assertSeeInOrder(['Akun', 'Profil', 'Panduan']);
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get(route('panduan'))->assertRedirect(route('login'));
    }
}
