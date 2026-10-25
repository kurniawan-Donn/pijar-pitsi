<?php

namespace Tests\Feature;

use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Peta lokasi Google Maps di bagian Kontak beranda, diatur lewat Profil Perusahaan.
 */
class PetaLokasiTest extends TestCase
{
    use RefreshDatabase;

    private const SEMATAN = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.2!2d110.38!3d-7.73!5e0!3m2!1sid!2sid!4v1700000000000';

    /**
     * @return array<string, mixed>
     */
    private function profil(array $data = []): array
    {
        return $data + [
            'nama' => 'PT Pilar Teknotama Sinergi',
            'alamat' => 'Gg. Mawar, Besi, Sukoharjo, Kec. Ngaglik',
            'kota' => 'Sleman',
            'tampilkan_peta' => '1',
        ];
    }

    private function simpan(array $data, string $role = User::ROLE_ADMIN)
    {
        return $this->actingAs(User::factory()->create(['role' => $role]))
            ->put(route('superadmin.perusahaan.update'), $this->profil($data));
    }

    public function test_tanpa_sematan_peta_dibuat_dari_alamat_dan_kota(): void
    {
        $this->simpan([])->assertSessionHasNoErrors();

        $alamat = rawurlencode('Gg. Mawar, Besi, Sukoharjo, Kec. Ngaglik, Sleman');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('class="card peta-card"', false)
            ->assertSee('src="https://maps.google.com/maps?q='.$alamat.'&amp;output=embed"', false)
            ->assertSee('href="https://www.google.com/maps/search/?api=1&amp;query='.$alamat.'"', false)
            ->assertSee('Buka di Google Maps');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function pengelola(): array
    {
        return ['admin' => [User::ROLE_ADMIN], 'super admin' => [User::ROLE_SUPER_ADMIN]];
    }

    #[DataProvider('pengelola')]
    public function test_kode_iframe_dari_google_maps_disimpan_hanya_url_nya(string $role): void
    {
        $this->simpan(['peta_embed' => '<iframe src="'.self::SEMATAN.'" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'], $role)
            ->assertSessionHasNoErrors();

        $this->assertSame(self::SEMATAN, Perusahaan::first()->peta_embed_url);
        $this->get(route('home'))->assertSee('src="'.e(self::SEMATAN).'"', false);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function sematanTidakSah(): array
    {
        return [
            'situs lain' => ['<iframe src="https://contoh.test/maps/embed?pb=1"></iframe>'],
            'javascript' => ['javascript:alert(1)'],
            'tautan bagikan biasa' => ['https://maps.app.goo.gl/abc123'],
            'http' => ['http://www.google.com/maps/embed?pb=1'],
        ];
    }

    #[DataProvider('sematanTidakSah')]
    public function test_hanya_sematan_resmi_google_maps_yang_diterima(string $isian): void
    {
        $this->simpan(['peta_embed' => $isian])->assertSessionHasErrors('peta_embed');

        $this->assertNull(Perusahaan::first());
    }

    public function test_peta_bisa_disembunyikan(): void
    {
        $this->simpan(['tampilkan_peta' => '0', 'peta_embed' => self::SEMATAN])->assertSessionHasNoErrors();

        $this->assertFalse(Perusahaan::first()->tampilkan_peta);
        $this->get(route('home'))->assertOk()->assertDontSee('class="card peta-card"', false);

        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))
            ->get(route('superadmin.perusahaan.index'))
            ->assertSee('Peta sedang disembunyikan.');
    }

    public function test_form_profil_menampilkan_pratinjau_peta(): void
    {
        $this->simpan(['peta_embed' => self::SEMATAN]);

        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))
            ->get(route('superadmin.perusahaan.index'))
            ->assertOk()
            ->assertSee('Kode Sematan Google Maps')
            ->assertSee('title="Pratinjau peta lokasi"', false);
    }
}
