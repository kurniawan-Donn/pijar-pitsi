<?php

namespace Tests\Feature;

use App\Models\BiodataPeserta;
use App\Models\PesertaMagang;
use App\Models\User;
use App\Services\FotoProfil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Foto profil: unggah, potong persegi, hak akses, dan pas foto biodata.
 */
class FotoProfilTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    private function peserta(string $nama = 'Rina'): User
    {
        $akun = User::factory()->create(['role' => User::ROLE_PESERTA, 'name' => $nama]);
        PesertaMagang::create(['nama' => $nama, 'status' => 'aktif', 'user_id' => $akun->id]);

        return $akun;
    }

    public function test_foto_dipotong_persegi_400_piksel_dan_tercatat_di_log(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->from(route('profil.edit'))
            ->put(route('profil.foto'), ['foto' => UploadedFile::fake()->image('wajah.png', 800, 500)])
            ->assertRedirect(route('profil.edit'))
            ->assertSessionHas('success');

        $path = $admin->fresh()->foto_path;
        $this->assertStringStartsWith(FotoProfil::FOLDER.'/', $path);
        $this->assertStringEndsWith('.jpg', $path);

        [$lebar, $tinggi, $jenis] = getimagesizefromstring(Storage::disk('local')->get($path));
        $this->assertSame([FotoProfil::UKURAN, FotoProfil::UKURAN, IMAGETYPE_JPEG], [$lebar, $tinggi, $jenis]);

        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'ganti_foto_profil', 'user_id' => $admin->id]);
    }

    public function test_ganti_foto_menghapus_file_lama_dan_hapus_foto_kembali_ke_inisial(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN, 'name' => 'Sari']);

        $this->actingAs($admin)->put(route('profil.foto'), ['foto' => UploadedFile::fake()->image('a.jpg', 300, 300)]);
        $lama = $admin->fresh()->foto_path;

        $this->actingAs($admin)->put(route('profil.foto'), ['foto' => UploadedFile::fake()->image('b.jpg', 300, 300)]);
        $baru = $admin->fresh()->foto_path;

        $this->assertNotSame($lama, $baru);
        Storage::disk('local')->assertMissing($lama);
        Storage::disk('local')->assertExists($baru);

        $this->actingAs($admin->fresh())
            ->get(route('superadmin.dashboard'))
            ->assertSee(route('foto-profil.show', $admin), false);

        $this->actingAs($admin)->delete(route('profil.foto.hapus'))->assertSessionHas('success');

        $this->assertNull($admin->fresh()->foto_path);
        Storage::disk('local')->assertMissing($baru);
        $this->actingAs($admin->fresh())
            ->get(route('superadmin.dashboard'))
            ->assertSee('class="x-avatar x-avatar-inisial dash-user-avatar"', false)
            ->assertDontSee(route('foto-profil.show', $admin), false);
    }

    public function test_halaman_profil_menyediakan_unggah_foto(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('profil.edit'))
            ->assertOk()
            ->assertSee('Foto Profil')
            ->assertSee('Unggah Foto')
            ->assertSee(route('profil.foto'), false)
            ->assertDontSee('Hapus foto');
    }

    public function test_validasi_format_ukuran_dan_dimensi(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $kasus = [
            [UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'), 'Foto harus berformat JPG atau PNG.'],
            [UploadedFile::fake()->image('besar.jpg', 500, 500)->size(2100), 'Ukuran foto maksimal 2 MB.'],
            [UploadedFile::fake()->image('kecil.jpg', 60, 60), 'Foto minimal 100 × 100 piksel.'],
        ];

        foreach ($kasus as [$file, $pesan]) {
            $this->actingAs($admin)
                ->put(route('profil.foto'), ['foto' => $file])
                ->assertSessionHasErrors(['foto' => $pesan]);
        }

        $this->assertNull($admin->fresh()->foto_path);
    }

    public function test_foto_hanya_boleh_dilihat_pemilik_dan_admin(): void
    {
        $pemilik = $this->peserta('Rina');
        $lain = $this->peserta('Budi');

        $this->actingAs($pemilik)->put(route('profil.foto'), ['foto' => UploadedFile::fake()->image('r.jpg', 200, 200)]);
        $pemilik->refresh();

        $this->actingAs($pemilik)->get(route('foto-profil.show', $pemilik))->assertOk();
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))->get(route('foto-profil.show', $pemilik))->assertOk();
        $this->actingAs($lain)->get(route('foto-profil.show', $pemilik))->assertForbidden();

        auth()->logout();
        $this->get(route('foto-profil.show', $pemilik))->assertRedirect(route('login'));
    }

    public function test_pas_foto_wajib_di_progres_dan_sebelum_biodata_dikirim(): void
    {
        $akun = $this->peserta();
        $peserta = $akun->pesertaMagang;

        $tanpaFoto = BiodataPeserta::untuk($peserta)->progres();
        $this->assertContains('Pas foto', BiodataPeserta::untuk($peserta)->fieldWajibKosong());

        $this->actingAs($akun)
            ->put(route('peserta.biodata.update'), ['aksi' => 'kirim'])
            ->assertSessionHasErrors(['pas_foto' => 'Unggah pas foto sebelum biodata dikirim.']);

        $this->actingAs($akun)->put(route('profil.foto'), ['foto' => UploadedFile::fake()->image('r.jpg', 200, 200)]);

        $denganFoto = BiodataPeserta::untuk($peserta->fresh())->progres();
        $this->assertSame($tanpaFoto['terisi'] + 1, $denganFoto['terisi']);

        $this->actingAs($akun)
            ->get(route('peserta.biodata.edit'))
            ->assertSee('Pas Foto *')
            ->assertSee('Ganti Foto')
            ->assertSee(route('foto-profil.show', $akun->fresh()), false);
    }

    public function test_peserta_boleh_ganti_foto_setelah_biodata_disetujui_tanpa_persetujuan_ulang(): void
    {
        $akun = $this->peserta();
        $biodata = BiodataPeserta::create([
            'peserta_magang_id' => $akun->pesertaMagang->id,
            'nama_lengkap' => 'Rina',
            'status' => BiodataPeserta::STATUS_DISETUJUI,
        ]);

        $this->actingAs($akun)
            ->from(route('peserta.biodata.edit'))
            ->put(route('profil.foto'), ['foto' => UploadedFile::fake()->image('baru.jpg', 300, 300)])
            ->assertRedirect(route('peserta.biodata.edit'))
            ->assertSessionHas('success');

        $this->assertNotNull($akun->fresh()->foto_path);
        $this->assertSame(BiodataPeserta::STATUS_DISETUJUI, $biodata->fresh()->status);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'ganti_foto_profil', 'user_id' => $akun->id]);
    }

    public function test_foto_peserta_tampil_di_tabel_admin_atau_inisial_bila_belum_ada(): void
    {
        $akun = $this->peserta('Rina');
        $this->peserta('Budi');
        $this->actingAs($akun)->put(route('profil.foto'), ['foto' => UploadedFile::fake()->image('r.jpg', 200, 200)]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        foreach (['superadmin.peserta-magang.index', 'superadmin.monitoring.index'] as $route) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk()
                ->assertSee(route('foto-profil.show', $akun->fresh()), false)
                ->assertSee('aria-hidden="true">B</span>', false);
        }
    }
}
