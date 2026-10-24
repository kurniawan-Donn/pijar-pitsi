<?php

namespace Tests\Feature;

use App\Models\LogAktivitas;
use App\Models\PengajuanMagang;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KelolaAkunPesertaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->peserta = PesertaMagang::create(['nama' => 'Rina Peserta', 'status' => 'aktif']);
    }

    private function buatAkun(string $email = 'rina@contoh.test'): string
    {
        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.akun.store', $this->peserta), ['email' => $email])
            ->assertSessionHasNoErrors();

        return session('password_sementara');
    }

    public function test_admin_membuat_akun_dan_password_sementara_tampil_sekali(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.akun.store', $this->peserta), ['email' => 'rina@contoh.test']);

        $response->assertRedirect(route('superadmin.peserta-magang.show', $this->peserta).'#akun');
        $password = session('password_sementara');

        $this->assertMatchesRegularExpression('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{10}$/', $password);

        $akun = $this->peserta->fresh()->user;
        $this->assertSame('rina@contoh.test', $akun->email);
        $this->assertSame('Rina Peserta', $akun->name);
        $this->assertTrue($akun->isPeserta());
        $this->assertTrue($akun->wajib_ganti_password);
        $this->assertTrue(Hash::check($password, $akun->password));

        // Halaman berikutnya menampilkan password, halaman sesudahnya tidak lagi.
        $this->get(route('superadmin.peserta-magang.show', $this->peserta))->assertSee($password);
        $this->get(route('superadmin.peserta-magang.show', $this->peserta))->assertDontSee($password)->assertSee('rina@contoh.test');

        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'buat_akun_peserta', 'subjek_id' => $this->peserta->id]);
    }

    public function test_password_sementara_tidak_dicatat_di_log(): void
    {
        $password = $this->buatAkun();

        $this->assertDatabaseMissing('log_aktivitas', ['keterangan' => $password]);
        $this->assertStringNotContainsString($password, json_encode(LogAktivitas::all()->toArray()));
    }

    public function test_email_terisi_dari_data_pendaftaran(): void
    {
        $pengajuan = PengajuanMagang::factory()->disetujui()->create();
        $this->peserta->update(['pengajuan_magang_id' => $pengajuan->id]);

        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.show', $this->peserta))
            ->assertOk()
            ->assertSee('value="'.$pengajuan->mahasiswa->email.'"', false);
    }

    public function test_email_wajib_unik_dan_satu_peserta_satu_akun(): void
    {
        User::factory()->create(['email' => 'dipakai@contoh.test']);

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.akun.store', $this->peserta), ['email' => 'dipakai@contoh.test'])
            ->assertSessionHasErrors(['email' => 'Email login sudah digunakan.']);

        $this->buatAkun();

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.akun.store', $this->peserta), ['email' => 'lain@contoh.test'])
            ->assertSessionHas('akun_error', 'Peserta ini sudah memiliki akun login.');

        $this->assertSame(1, User::where('role', User::ROLE_PESERTA)->count());
    }

    public function test_peserta_bisa_login_dengan_password_sementara_lalu_wajib_ganti(): void
    {
        $password = $this->buatAkun();
        $this->post(route('logout'));

        $this->post(route('login.process'), ['email' => 'rina@contoh.test', 'password' => $password])
            ->assertRedirect(route('peserta.dashboard'));

        $this->get(route('peserta.dashboard'))->assertRedirect(route('profil.edit'));
    }

    public function test_reset_password_membuat_password_sementara_baru(): void
    {
        $lama = $this->buatAkun();
        $akun = $this->peserta->fresh()->user;
        $akun->update(['wajib_ganti_password' => false]);

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.akun.reset', $this->peserta))
            ->assertSessionHas('akun_status');

        $baru = session('password_sementara');

        $this->assertNotSame($lama, $baru);
        $this->assertTrue(Hash::check($baru, $akun->fresh()->password));
        $this->assertTrue($akun->fresh()->wajib_ganti_password);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'reset_password_peserta']);
    }

    public function test_nonaktifkan_dan_aktifkan_kembali(): void
    {
        $this->buatAkun();
        $akun = $this->peserta->fresh()->user;

        $this->actingAs($this->admin)->put(route('superadmin.peserta-magang.akun.status', $this->peserta))
            ->assertSessionHas('akun_status', 'Akun login berhasil dinonaktifkan.');
        $this->assertFalse($akun->fresh()->aktif);

        $this->actingAs($this->admin)->put(route('superadmin.peserta-magang.akun.status', $this->peserta))
            ->assertSessionHas('akun_status', 'Akun login berhasil diaktifkan.');
        $this->assertTrue($akun->fresh()->aktif);

        $this->assertSame(2, LogAktivitas::where('aksi', 'ubah_status_akun_peserta')->count());
    }

    public function test_hapus_akun_tidak_menghapus_data_peserta(): void
    {
        $this->buatAkun();
        $akunId = $this->peserta->fresh()->user_id;

        $this->actingAs($this->admin)->delete(route('superadmin.peserta-magang.akun.destroy', $this->peserta))
            ->assertSessionHas('akun_status', 'Akun login berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $akunId]);
        $this->assertNull($this->peserta->fresh()->user_id);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'hapus_akun_peserta']);
    }

    public function test_aksi_akun_tanpa_akun_memberi_pesan(): void
    {
        foreach ([
            ['put', 'superadmin.peserta-magang.akun.reset'],
            ['put', 'superadmin.peserta-magang.akun.status'],
            ['delete', 'superadmin.peserta-magang.akun.destroy'],
        ] as [$method, $route]) {
            $this->actingAs($this->admin)
                ->{$method}(route($route, $this->peserta))
                ->assertSessionHas('akun_error', 'Peserta ini belum memiliki akun login.');
        }
    }

    public function test_hapus_peserta_ikut_menghapus_akunnya(): void
    {
        $this->buatAkun();
        $akunId = $this->peserta->fresh()->user_id;

        $this->actingAs($this->admin)->delete(route('superadmin.peserta-magang.destroy', $this->peserta));

        $this->assertDatabaseMissing('peserta_magangs', ['id' => $this->peserta->id]);
        $this->assertDatabaseMissing('users', ['id' => $akunId]);
    }

    public function test_peserta_tidak_bisa_mengelola_akun(): void
    {
        $this->buatAkun();
        $akun = $this->peserta->fresh()->user;
        $akun->update(['wajib_ganti_password' => false]);

        $this->actingAs($akun)
            ->put(route('superadmin.peserta-magang.akun.reset', $this->peserta))
            ->assertForbidden();
    }
}
