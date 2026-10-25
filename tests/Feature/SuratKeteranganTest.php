<?php

namespace Tests\Feature;

use App\Models\BiodataPeserta;
use App\Models\Divisi;
use App\Models\FeedbackPeserta;
use App\Models\PengaturanMagang;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use App\Models\SuratKeterangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SuratKeteranganTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $akun;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-11-30 10:00:00');

        PengaturanMagang::ambil()->update(['kota_surat' => 'Yogyakarta', 'menyetujui_nama' => 'Manajer Contoh']);

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->akun = User::factory()->create(['role' => User::ROLE_PESERTA]);
        $this->peserta = PesertaMagang::create([
            'nama' => 'Dony',
            'nim' => '243307072',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-01',
            'periode_selesai' => '2026-11-30',
            'user_id' => $this->akun->id,
        ]);
    }

    /**
     * Biodata disetujui, penilaian, feedback, dan rotasi divisi.
     */
    private function lengkapiSyarat(PesertaMagang $peserta): void
    {
        BiodataPeserta::create([
            'peserta_magang_id' => $peserta->id,
            'nama_lengkap' => $peserta->nama.' Lengkap',
            'nim' => $peserta->nim,
            'perguruan_tinggi' => 'Politeknik Contoh',
            'fakultas_jurusan' => 'Teknologi Informasi',
            'status' => BiodataPeserta::STATUS_DISETUJUI,
        ]);

        PenilaianPeserta::create(['peserta_magang_id' => $peserta->id, 'nilai_akhir' => 86.4, 'predikat' => 'Sangat Memuaskan']);

        FeedbackPeserta::create([
            'peserta_magang_id' => $peserta->id,
            'skor' => array_fill_keys(range(1, 10), 5),
            'paling_disukai' => 'a', 'kendala' => 'b', 'perlu_diperbaiki' => 'c', 'saran' => 'd',
            'rekomendasi' => 'bersedia', 'evaluasi_keseluruhan' => 'e', 'pernyataan' => true, 'jumlah_kirim' => 1,
        ]);

        $umum = Divisi::firstOrCreate(['nama' => 'Unit Umum']);
        $konsultan = Divisi::firstOrCreate(['nama' => 'Divisi Konsultan']);
        $peserta->penempatans()->create(['periode' => '2026-06', 'divisi_id' => $umum->id]);
        $peserta->penempatans()->create(['periode' => '2026-08', 'divisi_id' => $konsultan->id]);
    }

    private function terbitkan(PesertaMagang $peserta, array $data = []): TestResponse
    {
        return $this->actingAs($this->admin)->put(
            route('superadmin.peserta-magang.surat.terbitkan', $peserta),
            $data + ['tanggal_surat' => '2026-11-30', 'tampilkan_nilai' => 0]
        );
    }

    public function test_draft_dibuat_otomatis_saat_status_menjadi_selesai(): void
    {
        $this->assertNull($this->peserta->suratKeterangan);

        $this->peserta->update(['nama' => 'Dony K']);
        $this->assertSame(0, SuratKeterangan::count());

        $this->peserta->update(['status' => 'selesai']);

        $surat = $this->peserta->fresh()->suratKeterangan;
        $this->assertSame(SuratKeterangan::DRAFT, $surat->status);
        $this->assertNull($surat->nomor_surat);

        // Mengubah data lain tidak membuat draft ganda.
        $this->peserta->update(['nama' => 'Dony Lagi']);
        $this->assertSame(1, SuratKeterangan::count());
    }

    public function test_tidak_bisa_terbit_sebelum_syarat_terpenuhi(): void
    {
        $this->peserta->update(['status' => 'selesai']);

        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.surat', $this->peserta))
            ->assertOk()
            ->assertSeeInOrder(['<li class="belum">', 'Biodata disetujui'], false)
            ->assertDontSee('Terbitkan Surat');

        $this->terbitkan($this->peserta)
            ->assertSessionHas('error', 'Surat belum bisa diterbitkan: Biodata belum disetujui. Penilaian akhir belum diisi. Peserta belum mengirim feedback.');

        $this->assertSame(SuratKeterangan::DRAFT, $this->peserta->fresh()->suratKeterangan->status);
    }

    public function test_terbitkan_dengan_nomor_otomatis_dan_salinan_data(): void
    {
        $this->lengkapiSyarat($this->peserta);
        $this->peserta->update(['status' => 'selesai']);

        $this->terbitkan($this->peserta, ['tampilkan_nilai' => 1])
            ->assertRedirect(route('superadmin.peserta-magang.surat', $this->peserta))
            ->assertSessionHas('success', 'Surat 001/SKM/XI/2026 diterbitkan. Peserta sudah bisa mengunduhnya.');

        $surat = $this->peserta->fresh()->suratKeterangan;

        $this->assertSame(SuratKeterangan::TERBIT, $surat->status);
        $this->assertSame('001/SKM/XI/2026', $surat->nomor_surat);
        $this->assertSame('Dony Lengkap', $surat->data['nama']);
        $this->assertSame(['Unit Umum', 'Divisi Konsultan'], $surat->data['divisi']);
        $this->assertSame(86.4, $surat->data['nilai_akhir']);
        $this->assertSame('Manajer Contoh', $surat->data['penanda_tangan']['nama']);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'terbitkan_surat']);

        // Peserta kedua mendapat nomor berikutnya.
        $kedua = PesertaMagang::create(['nama' => 'Eka', 'nim' => '2', 'status' => 'aktif', 'periode_mulai' => '2026-06-01', 'periode_selesai' => '2026-11-30']);
        $this->lengkapiSyarat($kedua);
        $kedua->update(['status' => 'selesai']);

        $this->terbitkan($kedua)->assertSessionHas('success');
        $this->assertSame('002/SKM/XI/2026', $kedua->fresh()->suratKeterangan->nomor_surat);
        $this->assertNull($kedua->fresh()->suratKeterangan->data['nilai_akhir']);
    }

    public function test_nomor_urut_mulai_lagi_tiap_tahun(): void
    {
        $this->lengkapiSyarat($this->peserta);
        $this->peserta->update(['status' => 'selesai']);
        $this->terbitkan($this->peserta, ['tanggal_surat' => '2027-01-05']);

        $this->assertSame('001/SKM/I/2027', $this->peserta->fresh()->suratKeterangan->nomor_surat);
    }

    public function test_terbit_ulang_memakai_nomor_yang_sama(): void
    {
        $this->lengkapiSyarat($this->peserta);
        $this->peserta->update(['status' => 'selesai']);
        $this->terbitkan($this->peserta);

        // Data yang disetujui diubah, surat lama tetap memakai salinan lama.
        $this->peserta->biodata->update(['nama_lengkap' => 'Dony Kurniawan']);
        $this->assertSame('Dony Lengkap', $this->peserta->fresh()->suratKeterangan->data['nama']);

        $this->terbitkan($this->peserta, ['tanggal_surat' => '2026-12-02'])
            ->assertSessionHas('success', 'Surat 001/SKM/XI/2026 diterbitkan ulang dengan data terbaru.');

        $surat = $this->peserta->fresh()->suratKeterangan;
        $this->assertSame('001/SKM/XI/2026', $surat->nomor_surat);
        $this->assertSame('Dony Kurniawan', $surat->data['nama']);
        $this->assertSame('2026-12-02', $surat->tanggal_surat->toDateString());
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'terbitkan_ulang_surat']);
    }

    public function test_pdf_draft_dan_terbit(): void
    {
        $this->lengkapiSyarat($this->peserta);
        $this->peserta->update(['status' => 'selesai']);

        $draft = $this->actingAs($this->admin)->get(route('superadmin.peserta-magang.surat.pdf', $this->peserta));
        $draft->assertOk();
        $this->assertStringStartsWith('%PDF-', $draft->getContent());
        $this->assertStringContainsString('-draft.pdf', $draft->headers->get('content-disposition'));

        $this->terbitkan($this->peserta);

        $terbit = $this->actingAs($this->admin)->get(route('superadmin.peserta-magang.surat.pdf', $this->peserta));
        $this->assertStringContainsString('surat-keterangan-magang-dony-lengkap.pdf', $terbit->headers->get('content-disposition'));
    }

    public function test_peserta_hanya_bisa_mengunduh_surat_yang_sudah_terbit(): void
    {
        $this->lengkapiSyarat($this->peserta);
        $this->peserta->update(['status' => 'selesai']);

        $this->actingAs($this->akun)
            ->get(route('peserta.surat.show'))
            ->assertOk()
            ->assertSee('Surat keterangan belum terbit')
            ->assertSeeInOrder(['<li class="ok">', 'Feedback magang sudah dikirim'], false);

        $this->actingAs($this->akun)
            ->get(route('peserta.surat.unduh'))
            ->assertRedirect(route('peserta.surat.show'))
            ->assertSessionHas('error');

        $this->terbitkan($this->peserta);

        // Model akun dimuat ulang seperti pada request baru.
        $this->actingAs($this->akun->fresh())->get(route('peserta.surat.show'))->assertSee('001/SKM/XI/2026');

        $unduh = $this->actingAs($this->akun->fresh())->get(route('peserta.surat.unduh'));
        $unduh->assertOk();
        $this->assertStringStartsWith('%PDF-', $unduh->getContent());
    }

    public function test_feedback_terkunci_setelah_surat_terbit(): void
    {
        $this->lengkapiSyarat($this->peserta);
        $this->peserta->update(['status' => 'selesai']);
        $this->terbitkan($this->peserta);

        $this->actingAs($this->akun)
            ->get(route('peserta.feedback.edit'))
            ->assertOk()
            ->assertSee('feedback tidak bisa diubah lagi')
            ->assertDontSee('Kirim Ulang Feedback');

        $this->actingAs($this->akun)
            ->put(route('peserta.feedback.update'), ['skor' => array_fill_keys(range(1, 10), 1)])
            ->assertSessionHasErrors();

        $this->assertSame(5, (int) $this->peserta->fresh()->feedback->skor[1]);
    }

    public function test_buat_draft_manual_untuk_peserta_selesai_lama(): void
    {
        $this->peserta->update(['status' => 'selesai']);
        $this->peserta->suratKeterangan()->delete();

        $aktif = PesertaMagang::create(['nama' => 'Masih Aktif', 'status' => 'aktif']);

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.surat.draft', $aktif))
            ->assertSessionHas('error');

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.surat.draft', $this->peserta))
            ->assertSessionHas('success');

        $this->assertNotNull($this->peserta->fresh()->suratKeterangan);
    }

    public function test_detail_peserta_menampilkan_status_surat(): void
    {
        $this->lengkapiSyarat($this->peserta);
        $this->peserta->update(['status' => 'selesai']);

        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.show', $this->peserta))
            ->assertSee('Draft · belum diterbitkan');

        $this->terbitkan($this->peserta);

        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.show', $this->peserta))
            ->assertSee('Terbit · 001/SKM/XI/2026');
    }
}
