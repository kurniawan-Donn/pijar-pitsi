<?php

namespace Tests\Feature;

use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Logbook harian dari sisi admin. Hari ini = Rabu, 12 Agustus 2026.
 */
class VerifikasiLogbookHarianTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-08-12 10:00:00');
        Storage::fake('local');

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->peserta = PesertaMagang::create([
            'nama' => 'Dony',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-03',
            'periode_selesai' => '2026-11-30',
        ]);
    }

    private function hadir(string $tanggal): LogbookHarian
    {
        return LogbookHarian::create([
            'peserta_magang_id' => $this->peserta->id,
            'tanggal' => $tanggal,
            'kehadiran' => 'hadir',
            'pagi_masuk' => '08:00',
            'siang_keluar' => '16:30',
            'activity' => 'Kegiatan '.$tanggal,
        ]);
    }

    private function izin(): IzinPeserta
    {
        $izin = IzinPeserta::create([
            'peserta_magang_id' => $this->peserta->id,
            'jenis' => 'sakit',
            'tanggal_mulai' => '2026-08-10',
            'tanggal_selesai' => '2026-08-11',
            'keterangan' => 'Demam',
            'bukti_path' => 'izin/surat.pdf',
            'bukti_nama' => 'surat.pdf',
        ]);
        Storage::disk('local')->put('izin/surat.pdf', '%PDF-uji');

        foreach (['2026-08-10', '2026-08-11'] as $tanggal) {
            LogbookHarian::create([
                'peserta_magang_id' => $this->peserta->id,
                'tanggal' => $tanggal,
                'kehadiran' => 'sakit',
                'activity' => 'Demam',
                'izin_peserta_id' => $izin->id,
            ]);
        }

        return $izin;
    }

    public function test_halaman_logbook_admin(): void
    {
        $this->hadir('2026-08-12');
        $this->izin();

        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.logbook-harian', $this->peserta))
            ->assertOk()
            ->assertSee('Kegiatan 2026-08-12')
            ->assertSee('Sakit · 10 Agt – 11 Agt 2026')
            ->assertSee(route('superadmin.izin-bukti.show', IzinPeserta::first()))
            ->assertSee('Buka kunci')
            ->assertSee('Rp 50.000');

        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.show', $this->peserta))
            ->assertSee(route('superadmin.peserta-magang.logbook-harian', $this->peserta));
    }

    public function test_verifikasi_beberapa_hari_sekaligus(): void
    {
        $a = $this->hadir('2026-08-05');
        $b = $this->hadir('2026-08-06');
        $c = $this->hadir('2026-08-07');

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.logbook-harian.verifikasi', $this->peserta), [
                'logbook' => [$a->id, $b->id],
                'status' => 'diterima',
                'periode' => '2026-08',
            ])
            ->assertRedirect(route('superadmin.peserta-magang.logbook-harian', [$this->peserta, 'periode' => '2026-08']))
            ->assertSessionHas('success', '2 hari berhasil ditandai Diterima.');

        $this->assertSame('diterima', $a->fresh()->status_verifikasi);
        $this->assertSame($this->admin->id, $a->fresh()->diverifikasi_oleh);
        $this->assertSame('menunggu', $c->fresh()->status_verifikasi);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'verifikasi_logbook_harian']);
    }

    public function test_tolak_wajib_catatan_dan_hari_izin_tidak_ikut_verifikasi_harian(): void
    {
        $a = $this->hadir('2026-08-05');
        $this->izin();
        $hariIzin = LogbookHarian::whereNotNull('izin_peserta_id')->first();

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.logbook-harian.verifikasi', $this->peserta), [
                'logbook' => [$a->id],
                'status' => 'ditolak',
            ])
            ->assertSessionHasErrors('catatan_verifikasi');

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.logbook-harian.verifikasi', $this->peserta), [
                'logbook' => [$a->id, $hariIzin->id],
                'status' => 'ditolak',
                'catatan_verifikasi' => 'Activity terlalu singkat',
            ])
            ->assertSessionHas('success', '1 hari berhasil ditandai Ditolak.');

        $this->assertSame('Activity terlalu singkat', $a->fresh()->catatan_verifikasi);
        $this->assertSame('menunggu', $hariIzin->fresh()->status_verifikasi);
    }

    public function test_verifikasi_tidak_mengubah_logbook_peserta_lain(): void
    {
        $lain = PesertaMagang::create(['nama' => 'Lain', 'status' => 'aktif']);
        $milikLain = LogbookHarian::create([
            'peserta_magang_id' => $lain->id,
            'tanggal' => '2026-08-05',
            'kehadiran' => 'hadir',
        ]);

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.logbook-harian.verifikasi', $this->peserta), [
                'logbook' => [$milikLain->id],
                'status' => 'diterima',
            ])
            ->assertSessionHas('success', '0 hari berhasil ditandai Diterima.');

        $this->assertSame('menunggu', $milikLain->fresh()->status_verifikasi);
    }

    public function test_terima_dan_tolak_izin_diteruskan_ke_semua_harinya(): void
    {
        $izin = $this->izin();

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.izin.verifikasi', [$this->peserta, $izin]), ['status' => 'ditolak'])
            ->assertSessionHasErrors('catatan_verifikasi');

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.izin.verifikasi', [$this->peserta, $izin]), ['status' => 'diterima'])
            ->assertSessionHas('success');

        $this->assertSame('diterima', $izin->fresh()->status);
        $this->assertSame(['diterima'], $izin->logbooks()->pluck('status_verifikasi')->unique()->values()->all());
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'verifikasi_izin']);
    }

    public function test_izin_milik_peserta_lain_ditolak(): void
    {
        $izin = $this->izin();
        $lain = PesertaMagang::create(['nama' => 'Lain', 'status' => 'aktif']);

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.izin.verifikasi', [$lain, $izin]), ['status' => 'diterima'])
            ->assertNotFound();
    }

    public function test_buka_kunci_hanya_untuk_tanggal_dalam_masa_magang(): void
    {
        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.logbook-harian.buka-kunci', $this->peserta), ['tanggal' => '2026-05-01'])
            ->assertSessionHas('error');

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.logbook-harian.buka-kunci', $this->peserta), ['tanggal' => '2026-08-20'])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('buka_kunci_logbooks', 0);
    }

    public function test_admin_unduh_bukti_dan_pdf(): void
    {
        $logbook = $this->hadir('2026-08-12');
        $bukti = $logbook->buktis()->create(['path' => 'logbook/foto.jpg', 'nama_asli' => 'foto.jpg', 'mime' => 'image/jpeg', 'ukuran' => 10]);
        Storage::disk('local')->put('logbook/foto.jpg', 'gambar');

        $this->actingAs($this->admin)->get(route('superadmin.logbook-bukti.show', $bukti))->assertOk();

        $pdf = $this->actingAs($this->admin)->get(route('superadmin.peserta-magang.logbook-harian.cetak', [$this->peserta, 'periode' => '2026-08']));
        $pdf->assertOk();
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
    }

    public function test_peserta_tidak_bisa_memverifikasi(): void
    {
        $akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->peserta->update(['user_id' => $akun->id]);
        $logbook = $this->hadir('2026-08-12');

        $this->actingAs($akun)
            ->put(route('superadmin.peserta-magang.logbook-harian.verifikasi', $this->peserta), [
                'logbook' => [$logbook->id],
                'status' => 'diterima',
            ])
            ->assertForbidden();

        $this->assertSame('menunggu', $logbook->fresh()->status_verifikasi);
    }
}
