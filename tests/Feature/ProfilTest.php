<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfilTest extends TestCase
{
    use RefreshDatabase;

    private function pengguna(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@contoh.test',
            'password' => 'passwordLama1',
        ]);
    }

    public function test_halaman_profil_dapat_dibuka_dari_topbar(): void
    {
        $user = $this->pengguna();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertSee(route('profil.edit'));
        $this->actingAs($user)->get(route('profil.edit'))->assertOk()->assertSee('admin@contoh.test');
    }

    public function test_ubah_nama_dan_email(): void
    {
        $user = $this->pengguna();

        $this->actingAs($user)
            ->put(route('profil.update'), ['name' => 'Nama Baru', 'email' => 'baru@contoh.test'])
            ->assertRedirect(route('profil.edit'))
            ->assertSessionHas('success');

        $this->assertSame('Nama Baru', $user->fresh()->name);
        $this->assertSame('baru@contoh.test', $user->fresh()->email);
    }

    public function test_email_tidak_boleh_milik_pengguna_lain(): void
    {
        User::factory()->create(['email' => 'dipakai@contoh.test']);

        $this->actingAs($this->pengguna())
            ->put(route('profil.update'), ['name' => 'X', 'email' => 'dipakai@contoh.test'])
            ->assertSessionHasErrors(['email' => 'Email sudah digunakan.']);
    }

    public function test_ganti_password_dengan_password_lama_yang_benar(): void
    {
        $user = $this->pengguna();

        $this->actingAs($user)
            ->put(route('profil.password'), [
                'current_password' => 'passwordLama1',
                'password' => 'passwordBaru2',
                'password_confirmation' => 'passwordBaru2',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('passwordBaru2', $user->fresh()->password));
        $this->assertDatabaseHas('log_aktivitas', ['user_id' => $user->id, 'aksi' => 'ubah_password']);
    }

    public function test_password_lama_salah_ditolak(): void
    {
        $user = $this->pengguna();

        $this->actingAs($user)
            ->put(route('profil.password'), [
                'current_password' => 'salah',
                'password' => 'passwordBaru2',
                'password_confirmation' => 'passwordBaru2',
            ])
            ->assertSessionHasErrors(['current_password' => 'Password saat ini salah.']);

        $this->assertTrue(Hash::check('passwordLama1', $user->fresh()->password));
    }

    public function test_password_baru_harus_kuat_dan_berbeda(): void
    {
        $user = $this->pengguna();

        $this->actingAs($user)
            ->put(route('profil.password'), [
                'current_password' => 'passwordLama1',
                'password' => '12345678',
                'password_confirmation' => '12345678',
            ])
            ->assertSessionHasErrors('password');

        $this->actingAs($user)
            ->put(route('profil.password'), [
                'current_password' => 'passwordLama1',
                'password' => 'passwordLama1',
                'password_confirmation' => 'passwordLama1',
            ])
            ->assertSessionHasErrors(['password' => 'Password baru harus berbeda dari password saat ini.']);
    }
}
