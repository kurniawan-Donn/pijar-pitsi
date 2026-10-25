<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\PesertaMagang;
use App\Models\SuratKeterangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Dashboard peserta. Hari ini = Rabu, 12 Agustus 2026 (periode Agustus:
 * 28 Juli – 27 Agustus), batas isi mundur bawaan 7 hari.
 */
class DashboardPesertaTest extends TestCase
{
    use RefreshDatabase;

    private User $akun;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-08-12 10:00:00');

        $this->akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->peserta = PesertaMagang::create([
            'nama' => 'Dony',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-01',
            'periode_selesai' => '2026-11-30',
            'user_id' => $this->akun->id,
        ]);
    }

    private function logbook(string $tanggal, string $status = LogbookHarian::MENUNGGU, array $data = []): LogbookHarian
    {
        return LogbookHarian::create($data + [
            'peserta_magang_id' => $this->peserta->id,
            'tanggal' => $tanggal,
            'kehadiran' => 'hadir',
            'pagi_masuk' => '08:00',
            'siang_keluar' => '16:30',
            'activity' => 'Audit',
            'status_verifikasi' => $status,
        ]);
    }

    private function dashboard(): TestResponse
    {
        // fresh(): relasi peserta dari request sebelumnya tidak ikut terbawa.
        return $this->actingAs($this->akun->fresh())->get(route('peserta.dashboard'))->assertOk();
    }

    public function test_logbook_hari_ini_belum_diisi_menampilkan_tombol_isi_sekarang(): void
    {
        $this->dashboard()
            ->assertSee('<strong>Belum diisi</strong>', false)
            ->assertSee('Isi sekarang')
            ->assertSee(route('peserta.logbook.edit', '2026-08-12'), false);

        $this->logbook('2026-08-12');

        $this->dashboard()
            ->assertSee('Sudah diisi · Menunggu')
            ->assertDontSee('<strong>Belum diisi</strong>', false);
    }

    public function test_hari_libur_tidak_meminta_isi_logbook(): void
    {
        Carbon::setTestNow('2026-08-15 10:00:00');

        $this->dashboard()
            ->assertSee('Hari libur (Sabtu)')
            ->assertDontSee('<strong>Belum diisi</strong>', false);
    }

    public function test_ringkasan_periode_dan_perkiraan_uang_saku(): void
    {
        $this->logbook('2026-08-10', LogbookHarian::DITERIMA);
        $this->logbook('2026-08-11', LogbookHarian::MENUNGGU, ['siang_keluar' => null, 'pagi_keluar' => '12:00']);
        $this->logbook('2026-08-07', LogbookHarian::DITOLAK);

        $this->dashboard()
            ->assertSee('Periode Agustus 2026')
            ->assertSeeInOrder(['Hadir penuh', '1', 'Setengah hari', '1'])
            ->assertSee('Rp 75.000')
            ->assertSee('1 hari masih menunggu verifikasi');
    }

    public function test_logbook_ditolak_dan_review_tampil_dengan_catatan_admin(): void
    {
        $this->logbook('2026-08-10', LogbookHarian::DITOLAK, ['catatan_verifikasi' => 'Lengkapi output harian']);
        $this->logbook('2026-08-11', LogbookHarian::REVIEW, ['catatan_verifikasi' => 'Jam masuk perlu dicek']);
        $this->logbook('2026-08-12', LogbookHarian::DITERIMA);

        $this->dashboard()
            ->assertSee('2 logbook ditolak / perlu review admin.')
            ->assertSee('Lengkapi output harian')
            ->assertSee('Jam masuk perlu dicek')
            ->assertSee(route('peserta.logbook.edit', '2026-08-10'), false);
    }

    public function test_peringatan_hari_yang_hampir_terkunci(): void
    {
        // Batas 7 hari: 5–7 Agustus terkunci dalam 2 hari; 6 Agustus sudah diisi.
        $this->logbook('2026-08-06');

        $this->dashboard()
            ->assertSee('2 hari kerja belum diisi dan akan terkunci dalam 2 hari')
            ->assertSee('05 Agt, 07 Agt');
    }

    public function test_divisi_mentor_sisa_hari_dan_biodata(): void
    {
        $divisi = Divisi::create(['nama' => 'Unit Umum']);
        $this->peserta->penempatans()->create(['periode' => '2026-08', 'divisi_id' => $divisi->id, 'mentor_nama' => 'Pak Budi']);

        $this->dashboard()
            ->assertSee('Unit Umum')
            ->assertSee('Pak Budi')
            ->assertSee('110 hari')
            ->assertSee('Isi Biodata')
            ->assertSee('data wajib terisi');
    }

    public function test_kalender_mini_berwarna_per_status(): void
    {
        $this->logbook('2026-08-10', LogbookHarian::DITERIMA);
        $this->logbook('2026-08-11', LogbookHarian::DITOLAK);
        $this->logbook('2026-08-04', LogbookHarian::MENUNGGU, ['kehadiran' => 'sakit', 'pagi_masuk' => null, 'siang_keluar' => null]);

        $this->dashboard()
            ->assertSee('Kalender Kehadiran')
            ->assertSee('class="sel k-diterima" title="10 Agustus: Diterima"', false)
            ->assertSee('class="sel k-ditolak" title="11 Agustus: Ditolak"', false)
            ->assertSee('class="sel k-izin" title="04 Agustus: Izin / Sakit"', false)
            ->assertSee('class="sel k-libur" title="01 Agustus: Libur"', false)
            // 28 Juli sudah lewat batas 7 hari dan belum diisi.
            ->assertSee('class="sel k-terkunci" title="28 Juli: Terkunci"', false)
            ->assertSee('class="sel k-belum hari-ini-sel" title="12 Agustus: Belum diisi"', false)
            // Tanggal yang belum tiba tanpa warna.
            ->assertSee('class="sel" title="13 Agustus"', false);
    }

    public function test_izin_menunggu_verifikasi_tampil(): void
    {
        IzinPeserta::create([
            'peserta_magang_id' => $this->peserta->id,
            'jenis' => 'izin',
            'tanggal_mulai' => '2026-08-13',
            'tanggal_selesai' => '2026-08-14',
            'keterangan' => 'Ujian kampus',
        ]);

        $this->dashboard()
            ->assertSee('Izin / Sakit Menunggu Verifikasi')
            ->assertSee('Ujian kampus');
    }

    public function test_pengingat_feedback_saat_dibuka(): void
    {
        $this->dashboard()->assertDontSee('Form feedback magang sudah dibuka');

        Carbon::setTestNow('2026-11-02 10:00:00');

        $this->dashboard()
            ->assertSee('Form feedback magang sudah dibuka')
            ->assertSee(route('peserta.feedback.edit'), false);
    }

    public function test_pengingat_surat_keterangan_terbit(): void
    {
        $this->dashboard()->assertDontSee('Surat keterangan magang Anda sudah terbit');

        // Status selesai membuat draft surat otomatis.
        $this->peserta->update(['status' => 'selesai']);
        SuratKeterangan::where('peserta_magang_id', $this->peserta->id)->update(['status' => SuratKeterangan::TERBIT, 'nomor_surat' => '001/SKM/VIII/2026']);

        $this->dashboard()
            ->assertSee('Surat keterangan magang Anda sudah terbit.')
            ->assertSee(route('peserta.surat.show'), false)
            // Feedback terkunci setelah surat terbit, jadi tidak diingatkan lagi.
            ->assertDontSee('Form feedback magang sudah dibuka');
    }
}
