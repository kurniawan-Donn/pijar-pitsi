<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaturanWebsiteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Data lengkap form Kelola Konten.
     *
     * @return array<string, string>
     */
    private function dataKonten(): array
    {
        $data = [
            'informasi_terbaru_judul' => 'Informasi Terbaru',
            'pengumuman_judul' => 'Pengumuman',
            'persyaratan_judul' => 'Syarat Magang Baru',
            'alur_judul' => 'Alur Magang Baru',
            'google_form_url' => 'https://forms.gle/contoh',
        ];

        foreach ([1, 2, 3] as $nomor) {
            $data["pengumuman_card{$nomor}_judul"] = "Pengumuman {$nomor}";
            $data["persyaratan{$nomor}_judul"] = "Persyaratan {$nomor}";
        }

        foreach ([1, 2, 3, 4] as $nomor) {
            $data["alur{$nomor}_judul"] = "Alur {$nomor}";
        }

        return $data;
    }

    public function test_super_admin_dapat_menyimpan_konten_website(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)
            ->put(route('superadmin.pengaturan.update'), $this->dataKonten());

        $response->assertRedirect(route('superadmin.pengaturan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengaturan_websites', [
            'persyaratan_judul' => 'Syarat Magang Baru',
            'alur_judul' => 'Alur Magang Baru',
            'google_form_url' => 'https://forms.gle/contoh',
        ]);
    }

    public function test_tamu_diarahkan_ke_login_saat_menyimpan_konten(): void
    {
        $this->put(route('superadmin.pengaturan.update'), $this->dataKonten())
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('pengaturan_websites', 0);
    }
}
