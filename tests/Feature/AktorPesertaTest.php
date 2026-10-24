<?php

namespace Tests\Feature;

use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AktorPesertaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $akun
     * @param  array<string, mixed>  $peserta
     */
    private function peserta(array $akun = [], array $peserta = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => User::ROLE_PESERTA,
            'email' => 'peserta@contoh.test',
            'password' => 'sementara123',
        ], $akun));

        PesertaMagang::create(array_merge([
            'nama' => 'Peserta Uji',
            'status' => 'aktif',
            'user_id' => $user->id,
        ], $peserta));

        return $user;
    }

    private function login(string $password = 'sementara123'): TestResponse
    {
        return $this->post(route('login.process'), [
            'email' => 'peserta@contoh.test',
            'password' => $password,
        ]);
    }

    public function test_peserta_login_diarahkan_ke_dashboard_peserta(): void
    {
        $this->peserta();

        $this->login()->assertRedirect(route('peserta.dashboard'));

        $this->get(route('peserta.dashboard'))
            ->assertOk()
            ->assertSee('Peserta Uji')
            ->assertSee('Peserta Magang')
            ->assertDontSee(route('admin.pengajuan.index'));
    }

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        $this->peserta(['aktif' => false]);

        $this->from(route('login'))->login()
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Akun Anda sedang dinonaktifkan. Hubungi admin magang.']);

        $this->assertGuest();
    }

    public function test_akun_yang_dinonaktifkan_saat_login_langsung_dikeluarkan(): void
    {
        $user = $this->peserta();

        $this->actingAs($user);
        $user->update(['aktif' => false]);

        $this->get(route('peserta.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_password_sementara_wajib_diganti_sebelum_membuka_halaman_lain(): void
    {
        $user = $this->peserta(['wajib_ganti_password' => true]);

        $this->login()->assertRedirect(route('peserta.dashboard'));

        $this->get(route('peserta.dashboard'))
            ->assertRedirect(route('profil.edit'))
            ->assertSessionHas('warning');

        $this->get(route('profil.edit'))->assertOk()->assertSee('password sementara');

        $this->put(route('profil.password'), [
            'current_password' => 'sementara123',
            'password' => 'passwordBaru9',
            'password_confirmation' => 'passwordBaru9',
        ])->assertRedirect(route('peserta.dashboard'));

        $this->assertFalse($user->fresh()->wajib_ganti_password);
        $this->assertTrue(Hash::check('passwordBaru9', $user->fresh()->password));
        $this->get(route('peserta.dashboard'))->assertOk();
    }

    public function test_admin_dengan_password_biasa_tetap_kembali_ke_profil(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'password' => 'passwordLama1']);

        $this->actingAs($admin)->put(route('profil.password'), [
            'current_password' => 'passwordLama1',
            'password' => 'passwordBaru2',
            'password_confirmation' => 'passwordBaru2',
        ])->assertRedirect(route('profil.edit'));
    }

    public function test_akun_peserta_tanpa_data_peserta_ditolak(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PESERTA]);

        $this->actingAs($user)->get(route('peserta.dashboard'))->assertForbidden();
    }

    public function test_peserta_selesai_hanya_bisa_membaca(): void
    {
        Route::middleware(['web', 'auth', 'role:peserta', 'peserta.aktif'])
            ->post('/peserta/uji-simpan', fn () => 'tersimpan');

        $aktif = $this->peserta();
        $this->actingAs($aktif)->post('/peserta/uji-simpan')->assertOk();

        $selesai = $this->peserta(['email' => 'selesai@contoh.test'], ['status' => 'selesai']);

        $this->actingAs($selesai)
            ->from(route('peserta.dashboard'))
            ->post('/peserta/uji-simpan')
            ->assertRedirect(route('peserta.dashboard'))
            ->assertSessionHas('error', 'Masa magang Anda sudah selesai. Data hanya dapat dilihat.');

        $this->actingAs($selesai)->get(route('peserta.dashboard'))->assertOk();
    }

    public function test_kelola_pengguna_tidak_menampilkan_atau_mengubah_akun_peserta(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $peserta = $this->peserta();

        $this->actingAs($superAdmin)
            ->get(route('superadmin.pengguna.index'))
            ->assertOk()
            ->assertDontSee('peserta@contoh.test');

        $this->actingAs($superAdmin)->get(route('superadmin.pengguna.edit', $peserta))->assertNotFound();
        $this->actingAs($superAdmin)->delete(route('superadmin.pengguna.destroy', $peserta))->assertNotFound();

        $this->assertNotNull($peserta->fresh());
    }

    public function test_nomor_peserta_dibuat_otomatis_per_bulan_mulai_magang(): void
    {
        $juni1 = PesertaMagang::create(['nama' => 'A', 'status' => 'aktif', 'periode_mulai' => '2026-06-01']);
        $juni2 = PesertaMagang::create(['nama' => 'B', 'status' => 'aktif', 'periode_mulai' => '2026-06-15']);
        $juli = PesertaMagang::create(['nama' => 'C', 'status' => 'aktif', 'periode_mulai' => '2026-07-01']);

        $this->assertSame('MG202606001', $juni1->nomor_peserta);
        $this->assertSame('MG202606002', $juni2->nomor_peserta);
        $this->assertSame('MG202607001', $juli->nomor_peserta);
    }

    public function test_relasi_akun_dan_data_peserta(): void
    {
        $user = $this->peserta();

        $this->assertSame('Peserta Uji', $user->pesertaMagang->nama);
        $this->assertTrue($user->pesertaMagang->user->is($user));
        $this->assertSame('peserta.dashboard', $user->routeDashboard());
    }
}
