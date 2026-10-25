<?php

namespace Tests\Feature;

use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Identitas PILARA dan ikon seragam (tanpa emoji) di halaman utama.
 */
class TampilanPilaraTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Emoji dan simbol bergambar yang tidak boleh dipakai sebagai ikon.
     */
    private const POLA_EMOJI = '/[\x{1F300}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u';

    private function tanpaEmoji(string $html, string $halaman): void
    {
        // Bintang rating (★☆) adalah isi data, bukan ikon.
        $html = str_replace(['★', '☆'], '', $html);

        preg_match_all(self::POLA_EMOJI, $html, $temuan);
        $this->assertSame([], array_values(array_unique($temuan[0])), "Masih ada emoji/simbol ikon di {$halaman}.");
    }

    public function test_login_memakai_nama_dan_logo_pilara(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<title>Login - PILARA</title>', false)
            ->assertSee('images/logo-pitsi-128.png', false)
            ->assertSee('Tumbuh bersama, kokoh berkarya.');
    }

    public function test_sidebar_admin_memakai_ikon_svg_tanpa_emoji(): void
    {
        $response = $this->actingAs(User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]))
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('class="dash-brand-logo"', false)
            ->assertSee('<div class="dash-brand-title">PILARA</div>', false)
            ->assertSee('class="x-icon"', false)
            ->assertSee('data-buka-menu', false);

        $this->tanpaEmoji($response->getContent(), 'dashboard super admin');
    }

    public function test_dashboard_peserta_tanpa_emoji(): void
    {
        $akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        PesertaMagang::create([
            'nama' => 'Peserta Tampilan',
            'status' => 'aktif',
            'periode_mulai' => now()->subMonth()->toDateString(),
            'periode_selesai' => now()->addMonths(2)->toDateString(),
            'user_id' => $akun->id,
        ]);

        $this->tanpaEmoji($this->actingAs($akun)->get(route('peserta.dashboard'))->assertOk()->getContent(), 'dashboard peserta');
    }

    public function test_beranda_publik_memakai_logo_dan_tanpa_emoji(): void
    {
        $response = $this->get(route('home'))
            ->assertOk()
            ->assertSee('images/logo-pitsi-128.png', false)
            ->assertSee('PILARA adalah platform magang PT Pilar Teknotama Sinergi (PITSI)');

        $this->tanpaEmoji($response->getContent(), 'beranda');
    }
}
