<?php

namespace Tests\Feature;

use App\Http\Controllers\Website\PendaftaranController;
use App\Models\IzinPeserta;
use App\Models\LogbookBukti;
use App\Models\LogbookHarian;
use App\Models\Mahasiswa;
use App\Models\PengajuanMagang;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Fase 0: tamu dan pengguna lain tidak bisa membuka data atau file milik orang
 * lain dengan menebak / mengganti ID di URL.
 */
class KeamananAksesDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-08-12 10:00:00');
        Storage::fake('local');
    }

    private function pengajuanDenganDokumen(string $email, string $path): PengajuanMagang
    {
        Storage::disk('local')->put($path, "%PDF-{$email}");

        return PengajuanMagang::factory()
            ->for(Mahasiswa::factory()->create(['email' => $email]))
            ->denganDokumen($path)
            ->create();
    }

    private function peserta(string $nama): User
    {
        $akun = User::factory()->create(['role' => User::ROLE_PESERTA]);

        PesertaMagang::create([
            'nama' => $nama,
            'status' => 'aktif',
            'periode_mulai' => '2026-06-03',
            'periode_selesai' => '2026-11-30',
            'user_id' => $akun->id,
        ]);

        return $akun;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function isiLogbook(User $akun, string $tanggal, array $data = []): TestResponse
    {
        return $this->actingAs($akun)->put(route('peserta.logbook.update', $tanggal), $data + [
            'kehadiran' => 'hadir',
            'pagi_masuk' => '08:00',
            'siang_keluar' => '16:30',
            'activity' => 'Menyusun laporan',
            'output' => 'Draft',
            'lokasi' => 'Kantor',
        ]);
    }

    private function dokumenDariCekStatus(PengajuanMagang $pengajuan, string $email): string
    {
        $this->post(route('pengajuan.status'), [
            'status_kode' => $pengajuan->nomor(),
            'status_email' => $email,
        ])->assertSessionHas('status_pengajuan.0.dokumen_url');

        return session('status_pengajuan')[0]['dokumen_url'];
    }

    // ---------------------------------------------------------------
    // Dokumen pengajuan
    // ---------------------------------------------------------------

    public function test_dokumen_pengajuan_admin_tertutup_untuk_tamu_dan_peserta(): void
    {
        $pengajuan = $this->pengajuanDenganDokumen('rina@contoh.test', 'pengajuan/rina.pdf');

        $this->get(route('admin.pengajuan.dokumen', $pengajuan))->assertRedirect(route('login'));
        $this->get(route('admin.pengajuan.show', $pengajuan))->assertRedirect(route('login'));

        $peserta = $this->peserta('Peserta Iseng');
        $this->actingAs($peserta)->get(route('admin.pengajuan.dokumen', $pengajuan))->assertForbidden();
        $this->actingAs($peserta)->get(route('admin.pengajuan.show', $pengajuan))->assertForbidden();
    }

    public function test_admin_dan_super_admin_boleh_membuka_semua_dokumen(): void
    {
        $rina = $this->pengajuanDenganDokumen('rina@contoh.test', 'pengajuan/rina.pdf');
        $budi = $this->pengajuanDenganDokumen('budi@contoh.test', 'pengajuan/budi.pdf');

        foreach ([User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN] as $role) {
            $admin = User::factory()->create(['role' => $role]);

            foreach ([$rina, $budi] as $pengajuan) {
                $this->actingAs($admin)->get(route('admin.pengajuan.dokumen', $pengajuan))->assertOk();
            }
        }
    }

    public function test_policy_pengajuan_menolak_peserta_walau_lolos_middleware(): void
    {
        $pengajuan = $this->pengajuanDenganDokumen('rina@contoh.test', 'pengajuan/rina.pdf');
        $peserta = $this->peserta('Peserta');

        foreach (['view', 'update', 'delete'] as $ability) {
            $this->assertFalse(Gate::forUser($peserta)->allows($ability, $pengajuan), "peserta tidak boleh {$ability}");
        }

        $this->assertTrue(Gate::forUser(User::factory()->create(['role' => User::ROLE_ADMIN]))->allows('view', $pengajuan));
    }

    public function test_path_lama_pengajuan_admin_tidak_ada_lagi(): void
    {
        $pengajuan = $this->pengajuanDenganDokumen('rina@contoh.test', 'pengajuan/rina.pdf');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->assertStringStartsWith(url('/admin/pengajuan/'), route('admin.pengajuan.dokumen', $pengajuan));
        $this->actingAs($admin)->get("/pengajuan/{$pengajuan->id}/dokumen")->assertNotFound();
    }

    public function test_pendaftar_membuka_dokumennya_sendiri_lewat_tautan_cek_status(): void
    {
        $pengajuan = $this->pengajuanDenganDokumen('rina@contoh.test', 'pengajuan/rina.pdf');

        $url = $this->dokumenDariCekStatus($pengajuan, 'rina@contoh.test');

        $this->assertStringContainsString('signature=', $url);
        $this->assertSame('%PDF-rina@contoh.test', $this->get($url)->assertOk()->streamedContent());
    }

    public function test_tautan_dokumen_ditolak_bila_id_diganti_kedaluwarsa_atau_tanpa_signature(): void
    {
        $rina = $this->pengajuanDenganDokumen('rina@contoh.test', 'pengajuan/rina.pdf');
        $budi = $this->pengajuanDenganDokumen('budi@contoh.test', 'pengajuan/budi.pdf');

        $url = $this->dokumenDariCekStatus($rina, 'rina@contoh.test');

        // Mengganti ID Rina dengan ID Budi membuat signature tidak cocok.
        $diganti = str_replace("/pengajuan/dokumen/{$rina->id}?", "/pengajuan/dokumen/{$budi->id}?", $url);
        $this->assertNotSame($url, $diganti);
        $this->get($diganti)->assertForbidden();

        $this->get(route('pengajuan.dokumen', $budi))->assertForbidden();

        $this->travel(PendaftaranController::MASA_BERLAKU_DOKUMEN + 1)->minutes();
        $this->get($url)->assertForbidden();
    }

    public function test_cek_status_tidak_memberi_tautan_dokumen_orang_lain(): void
    {
        $rina = $this->pengajuanDenganDokumen('rina@contoh.test', 'pengajuan/rina.pdf');

        // Kode benar tetapi email bukan pemiliknya.
        $this->post(route('pengajuan.status'), [
            'status_kode' => $rina->nomor(),
            'status_email' => 'budi@contoh.test',
        ])->assertSessionMissing('status_pengajuan');
    }

    public function test_file_privat_tidak_bisa_dibuka_lewat_url_storage(): void
    {
        $this->pengajuanDenganDokumen('rina@contoh.test', 'pengajuan/rina.pdf');

        $this->assertFalse(Route::has('storage.local'));
        $this->get('/storage/pengajuan/rina.pdf')->assertNotFound();
    }

    // ---------------------------------------------------------------
    // Data peserta
    // ---------------------------------------------------------------

    public function test_peserta_tidak_bisa_membuka_atau_membatalkan_izin_peserta_lain(): void
    {
        $pemilik = $this->peserta('Pemilik');
        $lain = $this->peserta('Lain');

        $this->actingAs($pemilik)->post(route('peserta.izin.store'), [
            'jenis' => 'sakit',
            'tanggal_mulai' => '2026-08-12',
            'tanggal_selesai' => '2026-08-12',
            'keterangan' => 'Demam',
            'bukti' => UploadedFile::fake()->create('surat.pdf', 50, 'application/pdf'),
        ])->assertSessionHasNoErrors();

        $izin = IzinPeserta::firstOrFail();

        $this->actingAs($lain)->get(route('peserta.izin.bukti', $izin))->assertForbidden();
        $this->actingAs($lain)->delete(route('peserta.izin.destroy', $izin))->assertForbidden();
        $this->assertModelExists($izin);

        $this->actingAs($pemilik)->get(route('peserta.izin.bukti', $izin))->assertOk();
    }

    public function test_hapus_bukti_dengan_id_milik_peserta_lain_diabaikan(): void
    {
        $pemilik = $this->peserta('Pemilik');
        $lain = $this->peserta('Lain');

        $this->isiLogbook($pemilik, '2026-08-12', ['bukti' => [UploadedFile::fake()->image('foto.jpg')]]);
        $buktiPemilik = LogbookBukti::firstOrFail();

        $this->isiLogbook($lain, '2026-08-12', ['hapus_bukti' => [$buktiPemilik->id]])->assertSessionHasNoErrors();

        $this->assertModelExists($buktiPemilik);
        Storage::disk('local')->assertExists($buktiPemilik->path);
        $this->actingAs($lain)->get(route('peserta.logbook.bukti', $buktiPemilik))->assertForbidden();
    }

    public function test_peserta_tidak_bisa_mengubah_status_verifikasi_sendiri(): void
    {
        $akun = $this->peserta('Peserta');

        $this->isiLogbook($akun, '2026-08-12', [
            'status_verifikasi' => LogbookHarian::DITERIMA,
            'catatan_verifikasi' => 'Disetujui sendiri',
        ])->assertSessionHasNoErrors();

        $logbook = LogbookHarian::firstOrFail();
        $this->assertSame(LogbookHarian::MENUNGGU, $logbook->status_verifikasi);
        $this->assertNull($logbook->catatan_verifikasi);
    }

    public function test_pengguna_yang_sudah_login_diarahkan_dari_halaman_login(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]))
            ->get(route('login'))
            ->assertRedirect(route('superadmin.dashboard'));

        $this->actingAs($this->peserta('Peserta'))
            ->get(route('login'))
            ->assertRedirect(route('peserta.dashboard'));
    }
}
