<?php

namespace Tests\Feature;

use App\Models\FeedbackPeserta;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Magang selesai 30 November 2026, feedback dibuka mulai 30 Oktober 2026.
 */
class FeedbackMagangTest extends TestCase
{
    use RefreshDatabase;

    private User $akun;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-11-02 09:00:00');

        $this->akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->peserta = PesertaMagang::create([
            'nama' => 'Dony',
            'asal_kampus' => 'Politeknik Contoh',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-01',
            'periode_selesai' => '2026-11-30',
            'user_id' => $this->akun->id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $ubah
     * @return array<string, mixed>
     */
    private function jawaban(array $ubah = []): array
    {
        return array_merge([
            'skor' => [1 => 5, 2 => 4, 3 => 4, 4 => 5, 5 => 5, 6 => 4, 7 => 5, 8 => 3, 9 => 4, 10 => 5],
            'paling_disukai' => 'Proyek nyata bersama tim konsultan',
            'kendala' => 'Adaptasi dengan regulasi perbankan',
            'perlu_diperbaiki' => 'Jadwal orientasi lebih terstruktur',
            'saran' => 'Pertemuan rutin mingguan dengan mentor',
            'rekomendasi' => 'sangat_bersedia',
            'evaluasi_keseluruhan' => 'Sangat bermanfaat',
            'pernyataan' => '1',
        ], $ubah);
    }

    private function kirim(array $ubah = []): TestResponse
    {
        return $this->actingAs($this->akun)->put(route('peserta.feedback.update'), $this->jawaban($ubah));
    }

    public function test_feedback_terkunci_sebelum_sebulan_terakhir(): void
    {
        Carbon::setTestNow('2026-10-29 09:00:00');

        $this->actingAs($this->akun)
            ->get(route('peserta.feedback.edit'))
            ->assertOk()
            ->assertSee('Feedback belum dibuka')
            ->assertSee('Jumat, 30 Oktober 2026')
            ->assertSee('aria-label="Terkunci"', false);

        $this->kirim()->assertSessionHas('error', 'Feedback belum dibuka.');
        $this->assertSame(0, FeedbackPeserta::count());
    }

    public function test_feedback_dibuka_dan_berisi_pertanyaan_sheet(): void
    {
        $this->actingAs($this->akun)
            ->get(route('peserta.feedback.edit'))
            ->assertOk()
            ->assertDontSee('aria-label="Terkunci"', false)
            ->assertSee('Pembimbing mudah dihubungi ketika saya membutuhkan bantuan atau konsultasi.')
            ->assertSee('Hal apa yang paling Anda sukai selama mengikuti program magang?')
            ->assertSee('Ragu-ragu')
            ->assertSee('Bagaimana Anda menilai pengalaman magang secara keseluruhan?');
    }

    public function test_semua_pertanyaan_wajib(): void
    {
        $skor = $this->jawaban()['skor'];
        unset($skor[3]);

        $this->kirim(['skor' => $skor, 'kendala' => '', 'rekomendasi' => '', 'pernyataan' => '0'])
            ->assertSessionHasErrors(['skor.3', 'kendala', 'rekomendasi', 'pernyataan' => 'Centang pernyataan kejujuran sebelum mengirim feedback.']);

        $this->kirim(['skor' => [1 => 6] + $skor])->assertSessionHasErrors('skor.1');

        $this->assertSame(0, FeedbackPeserta::count());
    }

    public function test_kirim_dan_kirim_ulang(): void
    {
        $this->kirim()
            ->assertRedirect(route('peserta.feedback.edit'))
            ->assertSessionHas('success', 'Feedback berhasil dikirim. Terima kasih!');

        $feedback = $this->peserta->fresh()->feedback;
        $this->assertSame(4.4, $feedback->rataRata());
        $this->assertSame(1, $feedback->jumlah_kirim);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'kirim_feedback', 'user_id' => $this->akun->id]);

        Carbon::setTestNow('2026-11-05 09:00:00');

        $this->kirim(['saran' => 'Tambahkan sesi berbagi antar divisi'])
            ->assertSessionHas('success', 'Perubahan feedback berhasil dikirim. Terima kasih!');

        $feedback = $feedback->fresh();
        $this->assertSame(2, $feedback->jumlah_kirim);
        $this->assertSame('2026-11-02', $feedback->dikirim_pada->toDateString());
        $this->assertSame('Tambahkan sesi berbagi antar divisi', $feedback->saran);
        $this->assertSame(1, FeedbackPeserta::count());
    }

    public function test_peserta_selesai_tetap_bisa_mengirim_feedback(): void
    {
        Carbon::setTestNow('2026-12-03 09:00:00');
        $this->peserta->update(['status' => 'selesai']);

        $this->kirim()->assertSessionHas('success');
        $this->assertNotNull($this->peserta->fresh()->feedback);

        // Selain feedback tetap hanya baca.
        $this->actingAs($this->akun)
            ->put(route('peserta.biodata.update'), ['aksi' => 'simpan'])
            ->assertSessionHas('error', 'Masa magang Anda sudah selesai. Data hanya dapat dilihat.');
    }

    public function test_admin_melihat_detail_dan_rekap(): void
    {
        $this->kirim();

        $lain = PesertaMagang::create(['nama' => 'Eka', 'status' => 'selesai', 'periode_mulai' => '2026-01-01', 'periode_selesai' => '2026-03-31']);
        FeedbackPeserta::create([
            'peserta_magang_id' => $lain->id,
            'skor' => array_fill_keys(range(1, 10), 3),
            'paling_disukai' => 'Lingkungan kerja',
            'kendala' => 'Jarak kantor',
            'perlu_diperbaiki' => 'Fasilitas',
            'saran' => 'Tidak ada',
            'rekomendasi' => 'ragu',
            'evaluasi_keseluruhan' => 'Cukup',
            'pernyataan' => true,
            'dikirim_pada' => now(),
            'jumlah_kirim' => 1,
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('superadmin.peserta-magang.feedback', $this->peserta))
            ->assertOk()
            ->assertSee('rata-rata 4,40 / 5')
            ->assertSee('Proyek nyata bersama tim konsultan')
            ->assertSee('Sangat Bersedia');

        $this->actingAs($admin)
            ->get(route('superadmin.feedback.index'))
            ->assertOk()
            ->assertSee('3,70 / 5')
            ->assertSee('1 / 2')
            ->assertSee('Jarak kantor')
            ->assertSee('Dony');

        $this->actingAs($admin)
            ->get(route('superadmin.feedback.index', ['dari' => '2026-11-01']))
            ->assertOk()
            ->assertSee('Dony')
            ->assertDontSee('Jarak kantor');

        $this->actingAs($admin)
            ->get(route('superadmin.peserta-magang.show', $this->peserta))
            ->assertSee('Sudah dikirim · rata-rata 4,40');
    }

    public function test_laporan_dan_export_feedback(): void
    {
        $this->kirim();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('superadmin.laporan.index', ['jenis' => 'feedback']))
            ->assertOk()
            ->assertSee('Rekap Feedback Magang')
            ->assertSee('P10')
            ->assertSee('Sangat Bersedia');

        $csv = $this->actingAs($admin)->get(route('superadmin.laporan.csv', ['jenis' => 'feedback']));
        $csv->assertOk();
        $this->assertStringContainsString('1;Dony;"Politeknik Contoh";30/11/2026;5;4;4;5;5;4;5;3;4;5;4,40;"Sangat Bersedia"', $csv->streamedContent());

        $this->actingAs($admin)->get(route('superadmin.laporan.xlsx', ['jenis' => 'feedback']))->assertOk();
    }
}
