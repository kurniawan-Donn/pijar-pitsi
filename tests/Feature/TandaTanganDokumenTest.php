<?php

namespace Tests\Feature;

use App\Models\BiodataPeserta;
use App\Models\FeedbackPeserta;
use App\Models\PengaturanMagang;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use App\Models\SuratKeterangan;
use App\Models\User;
use App\Services\CetakSuratKeterangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Gambar tanda tangan & stempel (Pengaturan Magang), pencetakannya di PDF,
 * dan QR verifikasi surat keterangan.
 */
class TandaTanganDokumenTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $admin;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-11-30 10:00:00');
        Storage::fake('local');

        PengaturanMagang::ambil()->update([
            'kota_surat' => 'Yogyakarta',
            'mengetahui_nama' => 'Staf Contoh',
            'menyetujui_nama' => 'Manajer Contoh',
            'menyetujui_jabatan' => 'Manager GA & FA',
        ]);

        $this->superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->peserta = PesertaMagang::create([
            'nama' => 'Dony',
            'nim' => '243307072',
            'asal_kampus' => 'Politeknik Contoh',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-01',
            'periode_selesai' => '2026-11-30',
        ]);
    }

    /**
     * Data pengaturan yang wajib ikut terkirim saat menyimpan.
     *
     * @return array<string, mixed>
     */
    private function formPengaturan(array $data = []): array
    {
        $pengaturan = PengaturanMagang::ambil();

        return $data + $pengaturan->only([
            'batas_isi_mundur_hari', 'tanggal_awal_periode', 'kota_surat', 'format_nomor_surat',
            'mengetahui_nama', 'mengetahui_jabatan', 'menyetujui_nama', 'menyetujui_jabatan',
        ]);
    }

    private function unggahSemua(): void
    {
        $this->actingAs($this->superAdmin)
            ->put(route('superadmin.pengaturan-magang.update'), $this->formPengaturan([
                'ttd_mengetahui' => UploadedFile::fake()->image('ttd-staf.png', 900, 300),
                'ttd_menyetujui' => UploadedFile::fake()->image('ttd-manajer.png', 600, 200),
                'stempel' => UploadedFile::fake()->image('stempel.jpg', 500, 500),
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('superadmin.pengaturan-magang.index'));
    }

    private function siapTerbit(): void
    {
        BiodataPeserta::create([
            'peserta_magang_id' => $this->peserta->id,
            'nama_lengkap' => 'Dony Kurniawan',
            'nim' => '243307072',
            'perguruan_tinggi' => 'Politeknik Contoh',
            'fakultas_jurusan' => 'Teknologi Informasi',
            'status' => BiodataPeserta::STATUS_DISETUJUI,
        ]);
        PenilaianPeserta::create(['peserta_magang_id' => $this->peserta->id, 'nilai_akhir' => 86.4, 'predikat' => 'Sangat Memuaskan']);
        FeedbackPeserta::create([
            'peserta_magang_id' => $this->peserta->id,
            'skor' => array_fill_keys(range(1, 10), 5),
            'paling_disukai' => 'a', 'kendala' => 'b', 'perlu_diperbaiki' => 'c', 'saran' => 'd',
            'rekomendasi' => 'bersedia', 'evaluasi_keseluruhan' => 'e', 'pernyataan' => true, 'jumlah_kirim' => 1,
        ]);
        $this->peserta->update(['status' => 'selesai']);
    }

    private function terbitkan(): SuratKeterangan
    {
        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.surat.terbitkan', $this->peserta), ['tanggal_surat' => '2026-11-30', 'tampilkan_nilai' => 0])
            ->assertSessionHas('success');

        return $this->peserta->fresh()->suratKeterangan;
    }

    private function jumlahGambar(string $pdf): int
    {
        return preg_match_all('#/Subtype\s*/Image#', $pdf);
    }

    public function test_super_admin_mengunggah_tanda_tangan_dan_stempel_sebagai_png_privat(): void
    {
        $this->unggahSemua();

        $pengaturan = PengaturanMagang::ambil();

        foreach (['mengetahui_ttd_path', 'menyetujui_ttd_path', 'stempel_path'] as $kolom) {
            $this->assertStringStartsWith('tanda-tangan/', $pengaturan->{$kolom});
            $this->assertStringEndsWith('.png', $pengaturan->{$kolom});
            Storage::disk('local')->assertExists($pengaturan->{$kolom});
        }

        // Diperkecil sesuai batas (tanda tangan maks 600 × 300).
        [$lebar, $tinggi, $jenis] = getimagesizefromstring(Storage::disk('local')->get($pengaturan->mengetahui_ttd_path));
        $this->assertSame([600, 200, IMAGETYPE_PNG], [$lebar, $tinggi, $jenis]);

        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'ubah_pengaturan_magang']);

        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.pengaturan-magang.index'))
            ->assertSee('id="pratinjau-stempel"', false)
            ->assertSee(route('superadmin.pengaturan-magang.gambar', ['jenis' => 'stempel', 'v' => substr(md5($pengaturan->stempel_path), 0, 8)]), false);

        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.pengaturan-magang.gambar', 'stempel'))
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }

    public function test_admin_tidak_melihat_dan_tidak_bisa_mengubah_tanda_tangan(): void
    {
        $this->unggahSemua();

        $this->actingAs($this->admin)
            ->get(route('superadmin.pengaturan-magang.index'))
            ->assertOk()
            ->assertDontSee('Gambar Tanda Tangan');

        $this->actingAs($this->admin)->get(route('superadmin.pengaturan-magang.gambar', 'stempel'))->assertForbidden();
        $this->actingAs($this->superAdmin)->get(route('superadmin.pengaturan-magang.gambar', 'bukan-jenis'))->assertNotFound();
    }

    public function test_file_bukan_gambar_ditolak(): void
    {
        $this->actingAs($this->superAdmin)
            ->put(route('superadmin.pengaturan-magang.update'), $this->formPengaturan([
                'stempel' => UploadedFile::fake()->create('stempel.pdf', 10, 'application/pdf'),
            ]))
            ->assertSessionHasErrors(['stempel' => 'Gambar harus berformat PNG atau JPG.']);

        $this->assertNull(PengaturanMagang::ambil()->stempel_path);
    }

    public function test_hapus_gambar(): void
    {
        $this->unggahSemua();
        $path = PengaturanMagang::ambil()->stempel_path;

        $this->actingAs($this->superAdmin)
            ->put(route('superadmin.pengaturan-magang.update'), $this->formPengaturan(['hapus_stempel' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertNull(PengaturanMagang::ambil()->stempel_path);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_pdf_penilaian_memuat_tanda_tangan_dan_stempel(): void
    {
        PenilaianPeserta::create(['peserta_magang_id' => $this->peserta->id, 'nilai_akhir' => 86.4, 'predikat' => 'Sangat Memuaskan']);
        $url = route('superadmin.peserta-magang.penilaian.cetak', $this->peserta);

        $tanpa = $this->jumlahGambar($this->actingAs($this->admin)->get($url)->getContent());
        $this->unggahSemua();
        $dengan = $this->jumlahGambar($this->actingAs($this->admin)->get($url)->getContent());

        // Logo kop selalu ada; tanda tangan Mengetahui, Menyetujui, dan stempel menambah 3 gambar.
        $this->assertGreaterThanOrEqual($tanpa + 3, $dengan);
    }

    public function test_surat_terbit_menyalin_gambar_dan_diberi_kode_verifikasi_tetap(): void
    {
        $this->unggahSemua();
        $this->siapTerbit();
        $pengaturan = PengaturanMagang::ambil();

        $surat = $this->terbitkan();
        $kode = $surat->kode_verifikasi;

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{32}$/', $kode);
        $this->assertSame($pengaturan->menyetujui_ttd_path, $surat->data['penanda_tangan']['ttd_path']);
        $this->assertSame($pengaturan->stempel_path, $surat->data['penanda_tangan']['stempel_path']);

        // Terbit ulang tidak mengganti kode (QR lama tetap berlaku).
        $this->assertSame($kode, $this->terbitkan()->kode_verifikasi);

        // QR + tanda tangan + stempel tercetak; draft tidak punya QR.
        $pdf = $this->actingAs($this->admin)->get(route('superadmin.peserta-magang.surat.pdf', $this->peserta))->getContent();
        $this->assertGreaterThanOrEqual(3, $this->jumlahGambar($pdf));
    }

    public function test_mengganti_tanda_tangan_tidak_mengubah_surat_yang_sudah_terbit(): void
    {
        $this->unggahSemua();
        $this->siapTerbit();
        $surat = $this->terbitkan();
        $ttdLama = $surat->data['penanda_tangan']['ttd_path'];

        $this->actingAs($this->superAdmin)
            ->put(route('superadmin.pengaturan-magang.update'), $this->formPengaturan([
                'menyetujui_nama' => 'Manajer Baru',
                'ttd_menyetujui' => UploadedFile::fake()->image('ttd-baru.png', 600, 200),
            ]))
            ->assertSessionHasNoErrors();

        $this->assertNotSame($ttdLama, PengaturanMagang::ambil()->menyetujui_ttd_path);
        $this->assertSame($ttdLama, $surat->fresh()->data['penanda_tangan']['ttd_path']);
        // File lama disimpan karena masih dipakai surat terbit.
        Storage::disk('local')->assertExists($ttdLama);
    }

    public function test_surat_lama_tanpa_salinan_gambar_hanya_memakai_gambar_bila_nama_sama(): void
    {
        $this->unggahSemua();
        $this->siapTerbit();
        $surat = $this->terbitkan();
        $cetak = app(CetakSuratKeterangan::class);

        $hapusSalinanGambar = function (string $nama) use ($surat) {
            $data = $surat->data;
            $data['penanda_tangan'] = ['nama' => $nama, 'jabatan' => 'Manager GA & FA'];
            $surat->forceFill(['data' => $data])->save();
        };

        $hapusSalinanGambar('Manajer Contoh');
        $namaSama = $this->jumlahGambar($cetak->pdf($surat->fresh())->getContent());

        $hapusSalinanGambar('Manajer Lama');
        $namaBeda = $this->jumlahGambar($cetak->pdf($surat->fresh())->getContent());

        $this->assertGreaterThan($namaBeda, $namaSama);
        $this->assertGreaterThanOrEqual(1, $namaBeda); // QR tetap ada
    }

    public function test_halaman_verifikasi_hanya_menampilkan_nama_periode_dan_nomor(): void
    {
        $this->siapTerbit();
        $surat = $this->terbitkan();

        $this->get(route('verifikasi-surat', $surat->kode_verifikasi))
            ->assertOk()
            ->assertSee('Surat keterangan asli')
            ->assertSee('Dony Kurniawan')
            ->assertSee('01 Juni 2026')
            ->assertSee('30 November 2026')
            ->assertSee($surat->nomor_surat)
            ->assertDontSee('243307072')
            ->assertDontSee('Politeknik Contoh')
            ->assertDontSee('86,40');
    }

    public function test_kode_salah_atau_surat_belum_terbit_tidak_terverifikasi(): void
    {
        $this->get(route('verifikasi-surat', str_repeat('a', 32)))
            ->assertNotFound()
            ->assertSee('Surat tidak ditemukan');

        $this->siapTerbit();
        $surat = $this->peserta->fresh()->suratKeterangan;
        $surat->forceFill(['kode_verifikasi' => str_repeat('b', 32)])->save();

        $this->assertSame(SuratKeterangan::DRAFT, $surat->status);
        $this->get(route('verifikasi-surat', $surat->kode_verifikasi))->assertNotFound();
        $this->assertNull($surat->urlVerifikasi());
    }
}
