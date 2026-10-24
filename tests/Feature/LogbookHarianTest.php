<?php

namespace Tests\Feature;

use App\Models\HariLibur;
use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\PesertaMagang;
use App\Models\TarifUangSaku;
use App\Models\User;
use App\Services\KalenderLogbook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Logbook harian dari sisi peserta. Hari ini = Rabu, 12 Agustus 2026
 * (periode Agustus: 28 Juli – 27 Agustus 2026), batas isi mundur 7 hari.
 */
class LogbookHarianTest extends TestCase
{
    use RefreshDatabase;

    private User $akun;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-08-12 10:00:00');
        Storage::fake('local');

        $this->akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->peserta = PesertaMagang::create([
            'nama' => 'Dony',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-03',
            'periode_selesai' => '2026-11-30',
            'user_id' => $this->akun->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function simpan(string $tanggal, array $data = []): TestResponse
    {
        return $this->actingAs($this->akun)->put(route('peserta.logbook.update', $tanggal), $data + [
            'kehadiran' => 'hadir',
            'pagi_masuk' => '08:00',
            'siang_keluar' => '16:30',
            'activity' => 'Menyusun laporan audit',
            'output' => 'Draft laporan',
            'lokasi' => 'Kantor Jogja',
        ]);
    }

    private function logbook(string $tanggal): ?LogbookHarian
    {
        return LogbookHarian::where('peserta_magang_id', $this->peserta->id)->whereDate('tanggal', $tanggal)->first();
    }

    public function test_tabel_periode_berjalan_dengan_libur_dan_kunci(): void
    {
        HariLibur::create(['tanggal' => '2026-08-17', 'keterangan' => 'HUT Kemerdekaan RI']);

        $this->actingAs($this->akun)
            ->get(route('peserta.logbook.index'))
            ->assertOk()
            ->assertSee('Agustus 2026 · 28 Juli s.d. 27 Agustus 2026')
            ->assertSee('HUT Kemerdekaan RI')
            ->assertSee('Sabtu')
            ->assertSee('belum tiba')
            ->assertSee('terkunci')
            ->assertSee(route('peserta.logbook.edit', '2026-08-12'))
            ->assertSee(route('peserta.logbook.edit', '2026-08-05'))
            ->assertDontSee(route('peserta.logbook.edit', '2026-08-04'))
            ->assertDontSee(route('peserta.logbook.edit', '2026-08-13'));
    }

    public function test_periode_lain_bisa_dipilih(): void
    {
        $this->actingAs($this->akun)
            ->get(route('peserta.logbook.index', ['periode' => '2026-06']))
            ->assertOk()
            ->assertSee('Juni 2026 · 03 Juni s.d. 27 Juni 2026');
    }

    public function test_isi_hari_penuh_seperti_buku_kerja(): void
    {
        $this->simpan('2026-08-12')
            ->assertRedirect(route('peserta.logbook.index', ['periode' => '2026-08']).'#hari-2026-08-12')
            ->assertSessionHas('success');

        $logbook = $this->logbook('2026-08-12');

        $this->assertSame('hadir', $logbook->kehadiran);
        $this->assertSame('08:00', $logbook->jam('pagi_masuk'));
        $this->assertSame('', $logbook->jam('pagi_keluar'));
        $this->assertSame('hari_penuh', $logbook->jenisKehadiran());
        $this->assertSame(LogbookHarian::MENUNGGU, $logbook->status_verifikasi);
    }

    public function test_setengah_hari_satu_sesi(): void
    {
        $this->simpan('2026-08-11', ['pagi_masuk' => '08:00', 'pagi_keluar' => '12:00', 'siang_keluar' => ''])
            ->assertSessionHasNoErrors();

        $this->assertSame('setengah_hari', $this->logbook('2026-08-11')->jenisKehadiran());
    }

    public function test_validasi_jam_dan_activity(): void
    {
        $this->simpan('2026-08-12', ['siang_keluar' => ''])
            ->assertSessionHasErrors('pagi_masuk');

        $this->simpan('2026-08-12', ['pagi_masuk' => '17:00'])
            ->assertSessionHasErrors('siang_keluar');

        $this->simpan('2026-08-12', ['activity' => ''])
            ->assertSessionHasErrors(['activity' => 'Activity wajib diisi untuk hari hadir.']);

        $this->assertNull($this->logbook('2026-08-12'));
    }

    public function test_tanggal_belum_tiba_dan_lewat_batas_ditolak(): void
    {
        $this->simpan('2026-08-13')->assertSessionHas('error', 'Tanggal tersebut belum tiba.');
        $this->simpan('2026-08-04')->assertSessionHas('error');
        $this->simpan('2026-12-01')->assertSessionHas('error', 'Tanggal tersebut di luar masa magang Anda.');

        $this->actingAs($this->akun)->get(route('peserta.logbook.edit', '2026-08-04'))->assertRedirect();

        $this->assertSame(0, LogbookHarian::count());
    }

    public function test_tanggal_yang_dibuka_admin_bisa_diisi(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('superadmin.peserta-magang.logbook-harian.buka-kunci', $this->peserta), ['tanggal' => '2026-08-04'])
            ->assertSessionHas('success');

        $this->simpan('2026-08-04')->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->assertNotNull($this->logbook('2026-08-04'));
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'buka_kunci_logbook']);

        // Kunci kembali berlaku setelah masa buka habis.
        Carbon::setTestNow('2026-08-25 10:00:00');
        $this->assertSame(KalenderLogbook::LEWAT_BATAS, app(KalenderLogbook::class)->akses($this->peserta, Carbon::parse('2026-08-04')));
    }

    public function test_logbook_diterima_terkunci_dan_perubahan_kembali_menunggu(): void
    {
        $this->simpan('2026-08-12');
        $this->logbook('2026-08-12')->update(['status_verifikasi' => LogbookHarian::DITOLAK, 'catatan_verifikasi' => 'Lengkapi output']);

        $this->actingAs($this->akun)->get(route('peserta.logbook.edit', '2026-08-12'))->assertOk()->assertSee('Lengkapi output');

        $this->simpan('2026-08-12', ['output' => 'Laporan final']);
        $this->assertSame(LogbookHarian::MENUNGGU, $this->logbook('2026-08-12')->status_verifikasi);

        $this->logbook('2026-08-12')->update(['status_verifikasi' => LogbookHarian::DITERIMA]);

        $this->simpan('2026-08-12', ['activity' => 'Diubah'])->assertSessionHas('error');
        $this->actingAs($this->akun)->delete(route('peserta.logbook.destroy', '2026-08-12'))->assertSessionHas('error');
        $this->assertSame('Menyusun laporan audit', $this->logbook('2026-08-12')->activity);
    }

    public function test_libur_mengosongkan_jam(): void
    {
        $this->simpan('2026-08-08', ['kehadiran' => 'libur', 'activity' => 'Sabtu'])->assertSessionHasNoErrors();

        $logbook = $this->logbook('2026-08-08');
        $this->assertSame('libur', $logbook->kehadiran);
        $this->assertSame('', $logbook->jam('pagi_masuk'));
    }

    public function test_upload_dan_hapus_bukti(): void
    {
        $this->simpan('2026-08-12', ['bukti' => [
            UploadedFile::fake()->image('foto.jpg'),
            UploadedFile::fake()->create('notulen.pdf', 100, 'application/pdf'),
        ]])->assertSessionHasNoErrors();

        $logbook = $this->logbook('2026-08-12');
        $this->assertCount(2, $logbook->buktis);
        Storage::disk('local')->assertExists($logbook->buktis[0]->path);

        $this->actingAs($this->akun)->get(route('peserta.logbook.bukti', $logbook->buktis[0]))->assertOk();

        // Total bukti per hari maksimal 3.
        $this->simpan('2026-08-12', ['bukti' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')]])
            ->assertSessionHasErrors('bukti');

        $dihapus = $logbook->buktis[0];
        $this->simpan('2026-08-12', ['hapus_bukti' => [$dihapus->id]])->assertSessionHasNoErrors();

        $this->assertCount(1, $logbook->fresh()->buktis);
        Storage::disk('local')->assertMissing($dihapus->path);
    }

    public function test_bukti_tidak_boleh_dibuka_peserta_lain_dan_file_berbahaya_ditolak(): void
    {
        $this->simpan('2026-08-12', ['bukti' => [UploadedFile::fake()->image('foto.jpg')]]);
        $bukti = $this->logbook('2026-08-12')->buktis->first();

        $lain = User::factory()->create(['role' => User::ROLE_PESERTA]);
        PesertaMagang::create(['nama' => 'Lain', 'status' => 'aktif', 'user_id' => $lain->id]);

        $this->actingAs($lain)->get(route('peserta.logbook.bukti', $bukti))->assertForbidden();

        $this->simpan('2026-08-11', ['bukti' => [UploadedFile::fake()->create('skrip.php', 1, 'application/x-php')]])
            ->assertSessionHasErrors('bukti.0');
    }

    public function test_hapus_isian(): void
    {
        $this->simpan('2026-08-12', ['bukti' => [UploadedFile::fake()->image('foto.jpg')]]);
        $path = $this->logbook('2026-08-12')->buktis->first()->path;

        $this->actingAs($this->akun)->delete(route('peserta.logbook.destroy', '2026-08-12'))->assertSessionHas('success');

        $this->assertNull($this->logbook('2026-08-12'));
        Storage::disk('local')->assertMissing($path);
    }

    public function test_ajukan_sakit_beberapa_hari_melewati_akhir_pekan(): void
    {
        $this->actingAs($this->akun)
            ->post(route('peserta.izin.store'), [
                'jenis' => 'sakit',
                'tanggal_mulai' => '2026-08-07',
                'tanggal_selesai' => '2026-08-10',
                'keterangan' => 'Demam, surat dokter 4 hari',
            ])
            ->assertSessionHasErrors(['bukti' => 'Bukti (mis. surat dokter) wajib diunggah untuk sakit.']);

        $this->actingAs($this->akun)
            ->post(route('peserta.izin.store'), [
                'jenis' => 'sakit',
                'tanggal_mulai' => '2026-08-07',
                'tanggal_selesai' => '2026-08-10',
                'keterangan' => 'Demam, surat dokter 4 hari',
                'bukti' => UploadedFile::fake()->create('surat-dokter.pdf', 200, 'application/pdf'),
            ])
            ->assertSessionHas('success', 'Sakit untuk 2 hari kerja berhasil diajukan.');

        $izin = IzinPeserta::first();
        Storage::disk('local')->assertExists($izin->bukti_path);

        // Jumat & Senin terisi sakit; Sabtu & Minggu tetap libur.
        $this->assertSame('sakit', $this->logbook('2026-08-07')->kehadiran);
        $this->assertSame('sakit', $this->logbook('2026-08-10')->kehadiran);
        $this->assertNull($this->logbook('2026-08-08'));
        $this->assertSame($izin->id, $this->logbook('2026-08-10')->izin_peserta_id);

        // Hari dari izin tidak bisa diubah lewat form harian.
        $this->simpan('2026-08-10')->assertSessionHas('error');

        $this->actingAs($this->akun)->get(route('peserta.izin.bukti', $izin))->assertOk();
    }

    public function test_izin_tidak_menimpa_hari_hadir_dan_tidak_boleh_bentrok(): void
    {
        $this->simpan('2026-08-11');

        $this->actingAs($this->akun)
            ->post(route('peserta.izin.store'), [
                'jenis' => 'izin',
                'tanggal_mulai' => '2026-08-11',
                'tanggal_selesai' => '2026-08-12',
                'keterangan' => 'Urusan kampus',
            ])
            ->assertSessionHas('warning', 'Izin untuk 1 hari kerja berhasil diajukan. Tanggal yang sudah diisi hadir tidak diubah: 11 Agt.');

        $this->assertSame('hadir', $this->logbook('2026-08-11')->kehadiran);
        $this->assertSame('izin', $this->logbook('2026-08-12')->kehadiran);

        $this->actingAs($this->akun)
            ->post(route('peserta.izin.store'), [
                'jenis' => 'izin',
                'tanggal_mulai' => '2026-08-12',
                'tanggal_selesai' => '2026-08-12',
                'keterangan' => 'Lagi',
            ])
            ->assertSessionHasErrors(['tanggal_mulai' => 'Sudah ada pengajuan izin/sakit pada rentang tanggal tersebut.']);
    }

    public function test_batas_tanggal_izin(): void
    {
        $data = ['jenis' => 'izin', 'keterangan' => 'Acara keluarga'];

        $this->actingAs($this->akun)
            ->post(route('peserta.izin.store'), $data + ['tanggal_mulai' => '2026-08-01', 'tanggal_selesai' => '2026-08-03'])
            ->assertSessionHasErrors('tanggal_mulai');

        $this->actingAs($this->akun)
            ->post(route('peserta.izin.store'), $data + ['tanggal_mulai' => '2026-09-20', 'tanggal_selesai' => '2026-09-21'])
            ->assertSessionHasErrors(['tanggal_mulai' => 'Izin hanya bisa diajukan paling jauh 30 hari ke depan.']);

        // Izin untuk hari mendatang (masih dalam 30 hari) boleh.
        $this->actingAs($this->akun)
            ->post(route('peserta.izin.store'), $data + ['tanggal_mulai' => '2026-08-20', 'tanggal_selesai' => '2026-08-20'])
            ->assertSessionHas('success');
    }

    public function test_batalkan_izin_menghapus_hari_izin(): void
    {
        $this->actingAs($this->akun)->post(route('peserta.izin.store'), [
            'jenis' => 'izin',
            'tanggal_mulai' => '2026-08-12',
            'tanggal_selesai' => '2026-08-12',
            'keterangan' => 'Urusan kampus',
        ]);

        $izin = IzinPeserta::first();

        $this->actingAs($this->akun)->delete(route('peserta.izin.destroy', $izin))->assertSessionHas('success');

        $this->assertModelMissing($izin);
        $this->assertNull($this->logbook('2026-08-12'));
    }

    public function test_rekap_periode_dan_uang_saku_dengan_tarif_berubah(): void
    {
        TarifUangSaku::create(['berlaku_mulai' => '2026-08-10', 'tarif_hari_penuh' => 60000, 'tarif_setengah_hari' => 30000]);

        $buat = fn (string $tanggal, array $data) => LogbookHarian::create($data + [
            'peserta_magang_id' => $this->peserta->id,
            'tanggal' => $tanggal,
            'kehadiran' => 'hadir',
            'activity' => 'Kerja',
        ]);

        $buat('2026-08-05', ['pagi_masuk' => '08:00', 'siang_keluar' => '16:30']);                        // penuh, tarif lama 50.000
        $buat('2026-08-06', ['pagi_masuk' => '08:00', 'pagi_keluar' => '12:00']);                         // setengah, tarif lama 25.000
        $buat('2026-08-10', ['pagi_masuk' => '08:00', 'siang_keluar' => '16:30']);                        // penuh, tarif baru 60.000
        $buat('2026-08-11', ['pagi_masuk' => '08:00', 'siang_keluar' => '16:30', 'status_verifikasi' => 'ditolak']); // tidak dihitung
        $buat('2026-08-12', ['siang_masuk' => '13:00', 'siang_keluar' => '16:30', 'status_verifikasi' => 'review']); // setengah, 30.000
        $buat('2026-08-07', ['kehadiran' => 'izin', 'activity' => 'Izin']);
        $buat('2026-08-08', ['kehadiran' => 'hadir', 'pagi_masuk' => '08:00', 'siang_keluar' => '12:00']); // Sabtu masuk: penuh 50.000

        $kalender = app(KalenderLogbook::class);
        $periode = $kalender->pilihPeriode($this->peserta, '2026-08');
        $rekap = $kalender->rekap($kalender->hari($this->peserta, $periode));

        $this->assertSame(3, $rekap['hari_penuh']);
        $this->assertSame(2, $rekap['setengah_hari']);
        $this->assertSame(1, $rekap['izin']);
        $this->assertSame(1, $rekap['ditolak']);
        $this->assertSame(50000 + 25000 + 60000 + 30000 + 50000, $rekap['uang_saku']);
        $this->assertCount(2, $rekap['tarif']);

        // 28 Jul – 12 Agt: 12 hari kerja, 6 di antaranya terisi (Sabtu 8 Agt bukan hari kerja).
        $this->assertSame(6, $rekap['belum_diisi']);
        // 8 hari Sabtu/Minggu dalam periode (termasuk yang belum tiba), dikurangi Sabtu 8 Agt yang diisi hadir.
        $this->assertSame(7, $rekap['libur']);
    }

    public function test_unduh_pdf_periode(): void
    {
        $this->simpan('2026-08-12');

        $response = $this->actingAs($this->akun)->get(route('peserta.logbook.cetak', ['periode' => '2026-08']));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertStringContainsString('logbook-dony-2026-08.pdf', $response->headers->get('content-disposition'));
    }

    public function test_peserta_selesai_hanya_bisa_melihat_logbook(): void
    {
        $this->peserta->update(['status' => 'selesai']);

        $this->actingAs($this->akun)->get(route('peserta.logbook.index'))->assertOk();
        $this->simpan('2026-08-12')->assertSessionHas('error', 'Masa magang Anda sudah selesai. Data hanya dapat dilihat.');
        $this->assertNull($this->logbook('2026-08-12'));
    }
}
