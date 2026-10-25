<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function buatAdmin(): User
    {
        return User::factory()->create([
            'email' => 'admin@contoh.test',
            'password' => 'rahasia-benar',
            'role' => 'admin',
        ]);
    }

    private function loginGagal(): void
    {
        $this->from(route('login'))->post(route('login.process'), [
            'email' => 'admin@contoh.test',
            'password' => 'password-salah',
        ]);
    }

    public function test_website_menampilkan_tombol_masuk_peserta_bukan_login_admin(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Masuk Peserta')
            ->assertSee('href="'.route('login').'"', false)
            ->assertDontSee('Login Admin');
    }

    public function test_halaman_login_tetap_dapat_dibuka_langsung(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_admin_dapat_login_dengan_data_benar(): void
    {
        $this->buatAdmin();

        $this->post(route('login.process'), [
            'email' => 'admin@contoh.test',
            'password' => 'rahasia-benar',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_login_dikunci_setelah_lima_kali_gagal(): void
    {
        $this->buatAdmin();

        for ($i = 0; $i < 5; $i++) {
            $this->loginGagal();
        }

        // Password benar pun ditolak selama masa kunci.
        $this->from(route('login'))
            ->post(route('login.process'), [
                'email' => 'admin@contoh.test',
                'password' => 'rahasia-benar',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertStringContainsString(
            'Terlalu banyak percobaan login',
            session('errors')->first('email')
        );
    }

    public function test_login_kembali_bisa_setelah_masa_kunci_berakhir(): void
    {
        $this->buatAdmin();

        for ($i = 0; $i < 5; $i++) {
            $this->loginGagal();
        }

        $this->travel(61)->seconds();

        $this->post(route('login.process'), [
            'email' => 'admin@contoh.test',
            'password' => 'rahasia-benar',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_login_berhasil_mereset_hitungan_gagal(): void
    {
        $this->buatAdmin();

        for ($i = 0; $i < 4; $i++) {
            $this->loginGagal();
        }

        $this->post(route('login.process'), [
            'email' => 'admin@contoh.test',
            'password' => 'rahasia-benar',
        ])->assertRedirect(route('admin.dashboard'));

        $this->post(route('logout'));

        // Hitungan sudah direset, jadi 4 kali gagal lagi belum mengunci akun.
        for ($i = 0; $i < 4; $i++) {
            $this->loginGagal();
        }

        $this->post(route('login.process'), [
            'email' => 'admin@contoh.test',
            'password' => 'rahasia-benar',
        ])->assertRedirect(route('admin.dashboard'));
    }
}
