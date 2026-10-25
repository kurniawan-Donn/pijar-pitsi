<?php

namespace Tests\Feature;

use App\Http\Requests\SimpanPenilaianRequest;
use App\Models\AspekPenilaian;
use App\Models\KelompokPenilaian;
use App\Models\PengaturanMagang;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use App\Models\User;
use App\Services\TandaTanganDokumen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Penilaian mengikuti sheet PENILAIAN MHS MAGANG: Hard Skill 60% (7 aspek),
 * Soft Skill 40% (8 aspek), skala 1–100, predikat teks.
 */
class PenilaianTest extends TestCase
{
    use RefreshDatabase;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->peserta = PesertaMagang::create([
            'nama' => 'Peserta Dinilai',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-01',
            'periode_selesai' => '2026-11-30',
        ]);
    }

    private function user(string $role = 'admin'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    /**
     * Nilai per kelompok: semua aspek Hard Skill = $hard, Soft Skill = $soft.
     *
     * @return array<int, int>
     */
    private function nilai(int $hard, int $soft): array
    {
        return AspekPenilaian::aktif()->with('kelompok')->get()->mapWithKeys(fn (AspekPenilaian $aspek) => [
            $aspek->id => $aspek->kelompok->nama === 'Hard Skill' ? $hard : $soft,
        ])->all();
    }

    public function test_struktur_bawaan_mengikuti_buku_kerja(): void
    {
        $kelompok = KelompokPenilaian::with('aspekAktif')->orderBy('urutan')->get();

        $this->assertSame(['Hard Skill' => 60, 'Soft Skill' => 40], $kelompok->pluck('bobot', 'nama')->all());
        $this->assertCount(7, $kelompok[0]->aspekAktif);
        $this->assertCount(8, $kelompok[1]->aspekAktif);
        $this->assertSame('Pemahaman terkait Profile dan Proses Bisnis perusahaan', $kelompok[0]->aspekAktif->first()->nama);
        $this->assertSame(15, AspekPenilaian::aktif()->count());
    }

    /**
     * @return array<string, array{float, string}>
     */
    public static function contohPredikat(): array
    {
        return [
            '100' => [100.0, 'Sangat Memuaskan'],
            '80,01' => [80.01, 'Sangat Memuaskan'],
            '80' => [80.0, 'Memuaskan'],
            '60,01' => [60.01, 'Memuaskan'],
            '60' => [60.0, 'Baik'],
            '40' => [40.0, 'Cukup'],
            '20,01' => [20.01, 'Cukup'],
            '20' => [20.0, 'Perlu Pembinaan'],
            '1' => [1.0, 'Perlu Pembinaan'],
        ];
    }

    #[DataProvider('contohPredikat')]
    public function test_predikat_sesuai_tabel_buku_kerja(float $nilai, string $predikat): void
    {
        $this->assertSame($predikat, PenilaianPeserta::predikatUntuk($nilai));
    }

    public function test_nilai_akhir_rata_rata_kelompok_kali_bobot(): void
    {
        // Hard: (90 + 80) / 2 = 85; Soft: (70 + 80 + 90) / 3 = 80 → 85 × 60% + 80 × 40% = 83
        $this->assertSame(83.0, PenilaianPeserta::hitungNilaiAkhir([
            ['kelompok' => 'Hard', 'bobot_kelompok' => 60, 'nilai' => 90],
            ['kelompok' => 'Hard', 'bobot_kelompok' => 60, 'nilai' => 80],
            ['kelompok' => 'Soft', 'bobot_kelompok' => 40, 'nilai' => 70],
            ['kelompok' => 'Soft', 'bobot_kelompok' => 40, 'nilai' => 80],
            ['kelompok' => 'Soft', 'bobot_kelompok' => 40, 'nilai' => 90],
        ]));

        $this->assertSame(0.0, PenilaianPeserta::hitungNilaiAkhir([]));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function rolePengelola(): array
    {
        return ['admin' => ['admin'], 'super admin' => ['super_admin']];
    }

    #[DataProvider('rolePengelola')]
    public function test_admin_dan_super_admin_dapat_mengisi_penilaian(string $role): void
    {
        $user = $this->user($role);

        $this->actingAs($user)
            ->get(route('superadmin.peserta-magang.penilaian', $this->peserta))
            ->assertOk()
            ->assertSee('Hard Skill')
            ->assertSee('Bobot 60%')
            ->assertSee('Etika dan Sikap Profesional')
            ->assertSee('Sangat Memuaskan');

        $this->actingAs($user)
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), [
                'nilai' => $this->nilai(90, 80),
                'kekuatan' => 'Cepat belajar dan teliti.',
                'aspek_perbaikan' => 'Manajemen waktu.',
                'langkah_perbaikan' => 'Membuat rencana kerja harian.',
            ])
            ->assertRedirect(route('superadmin.peserta-magang.penilaian', $this->peserta))
            ->assertSessionHas('success', 'Penilaian disimpan. Nilai akhir 86,00 (Sangat Memuaskan).');

        $penilaian = $this->peserta->penilaian()->with('details')->first();

        $this->assertSame(86.0, $penilaian->nilai_akhir);
        $this->assertSame('Sangat Memuaskan', $penilaian->predikat);
        $this->assertSame('Cepat belajar dan teliti.', $penilaian->kekuatan);
        $this->assertSame($user->id, $penilaian->dinilai_oleh);
        $this->assertCount(15, $penilaian->details);
        $this->assertSame(['Hard Skill' => 90.0, 'Soft Skill' => 80.0], array_map(fn ($kelompok) => $kelompok['rata_rata'], $penilaian->nilaiPerKelompok()));
    }

    public function test_menyimpan_ulang_mengganti_nilai_lama(): void
    {
        $user = $this->user();

        $this->actingAs($user)->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilai(70, 70)]);
        $this->actingAs($user)->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilai(50, 60)]);

        $penilaian = $this->peserta->penilaian()->first();

        $this->assertSame(1, PenilaianPeserta::count());
        $this->assertSame(54.0, $penilaian->nilai_akhir);
        $this->assertSame('Baik', $penilaian->predikat);
        $this->assertSame(15, $penilaian->details()->count());
    }

    public function test_semua_aspek_wajib_dinilai_dengan_nilai_1_sampai_100(): void
    {
        $nilai = $this->nilai(80, 80);
        [$idPertama, $idKedua, $idKetiga] = array_keys($nilai);

        unset($nilai[$idPertama]);
        $nilai[$idKedua] = 120;
        $nilai[$idKetiga] = 0;

        $this->actingAs($this->user())
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $nilai])
            ->assertSessionHasErrors(["nilai.{$idPertama}", "nilai.{$idKedua}", "nilai.{$idKetiga}" => 'Nilai minimal 1.']);

        $this->assertSame(0, PenilaianPeserta::count());
    }

    public function test_nilai_lama_tidak_berubah_saat_aspek_atau_bobot_diubah(): void
    {
        $this->actingAs($this->user())
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilai(80, 80)]);

        AspekPenilaian::query()->update(['nama' => 'Diubah']);
        KelompokPenilaian::query()->update(['bobot' => 50]);

        $detail = $this->peserta->penilaian()->first()->details()->first();

        $this->assertSame('Pemahaman terkait Profile dan Proses Bisnis perusahaan', $detail->nama_aspek);
        $this->assertSame('Hard Skill', $detail->kelompok);
        $this->assertSame(60, $detail->bobot_kelompok);
    }

    public function test_nilai_tampil_di_daftar_dan_detail_peserta(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilai(78, 78)]);

        $this->actingAs($user)->get(route('superadmin.peserta-magang.index'))
            ->assertSee('78,00')
            ->assertSee('Memuaskan');

        $this->actingAs($user)->get(route('superadmin.peserta-magang.show', $this->peserta))
            ->assertSee('Nilai 78,00')
            ->assertSee('predikat Memuaskan');
    }

    public function test_cetak_lembar_penilaian_pdf(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->get(route('superadmin.peserta-magang.penilaian.cetak', $this->peserta))
            ->assertRedirect(route('superadmin.peserta-magang.penilaian', $this->peserta))
            ->assertSessionHas('error');

        $this->actingAs($user)
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilai(85, 82), 'kekuatan' => 'Teliti']);

        $pdf = $this->actingAs($user)->get(route('superadmin.peserta-magang.penilaian.cetak', $this->peserta));

        $pdf->assertOk();
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        $this->assertStringContainsString('penilaian-peserta-dinilai.pdf', $pdf->headers->get('content-disposition'));
    }

    /**
     * Isian terpanjang yang lolos validasi: satu paragraf penuh, atau paragraf maksimal
     * yang masing-masing sedikit lebih panjang dari satu baris cetak (paling boros tempat).
     *
     * @return array<string, array{string}>
     */
    public static function isianTerpanjang(): array
    {
        return [
            'satu paragraf' => ['paragraf'],
            'banyak baris' => ['baris'],
        ];
    }

    #[DataProvider('isianTerpanjang')]
    public function test_lembar_penilaian_isian_terpanjang_tetap_satu_halaman(string $bentuk): void
    {
        $user = $this->user();
        $maks = SimpanPenilaianRequest::MAKS_KESIMPULAN;
        $maksBaris = SimpanPenilaianRequest::MAKS_BARIS_KESIMPULAN;
        $isian = function (string $awal) use ($bentuk, $maks, $maksBaris): string {
            $kalimat = str_repeat($awal.' kalimat penjelasan yang cukup panjang untuk mengisi kolom. ', 100);

            if ($bentuk === 'paragraf') {
                return mb_substr($kalimat, 0, $maks);
            }

            // Satu baris cetak memuat ±170 karakter; paragraf 175 karakter terlipat menjadi 2 baris.
            $paragraf = [];
            $sisa = $maks;

            while (count($paragraf) < $maksBaris && $sisa > 1) {
                $panjang = min(175, $sisa - ($paragraf ? 1 : 0));
                $paragraf[] = mb_substr($kalimat, 0, $panjang);
                $sisa -= $panjang + ($paragraf ? 1 : 0);
            }

            return implode("\n", $paragraf);
        };

        $this->peserta->update([
            'nama' => 'Muhammad Abdurrahman Al-Fatih Wicaksono Putra Pratama',
            'asal_kampus' => 'Universitas Pembangunan Nasional Veteran Yogyakarta',
            'program_studi' => 'Teknik Informatika dan Sistem Informasi',
        ]);
        PengaturanMagang::ambil()->update([
            'kota_surat' => 'Yogyakarta',
            'mengetahui_nama' => 'Nama Penanda Tangan Mengetahui Yang Panjang',
            'mengetahui_jabatan' => 'Manajer Sumber Daya Manusia dan Umum',
            'menyetujui_nama' => 'Nama Penanda Tangan Menyetujui Yang Panjang',
            'menyetujui_jabatan' => 'Direktur Utama',
        ]);

        // Gambar tanda tangan & stempel ukuran terbesar ikut tercetak.
        Storage::fake('local');
        $tandaTangan = app(TandaTanganDokumen::class);
        $tandaTangan->simpan(PengaturanMagang::ambil(), 'ttd_mengetahui', UploadedFile::fake()->image('ttd-1.png', 600, 300));
        $tandaTangan->simpan(PengaturanMagang::ambil(), 'ttd_menyetujui', UploadedFile::fake()->image('ttd-2.png', 600, 300));
        $tandaTangan->simpan(PengaturanMagang::ambil(), 'stempel', UploadedFile::fake()->image('stempel.png', 400, 400));

        $this->actingAs($user)
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), [
                'nilai' => $this->nilai(85, 82),
                'kekuatan' => $isian('Kekuatan'),
                'aspek_perbaikan' => $isian('Perbaikan'),
                'langkah_perbaikan' => $isian('Langkah'),
            ])
            ->assertSessionHasNoErrors();

        $pdf = $this->actingAs($user)->get(route('superadmin.peserta-magang.penilaian.cetak', $this->peserta));

        $pdf->assertOk();
        $this->assertSame(1, preg_match_all('#/Type\s*/Page[^s]#', $pdf->getContent()), 'Lembar penilaian harus muat 1 halaman A4.');
    }

    public function test_kesimpulan_tidak_boleh_melebihi_batas_karakter(): void
    {
        $this->actingAs($this->user())
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), [
                'nilai' => $this->nilai(85, 82),
                'kekuatan' => str_repeat('a', SimpanPenilaianRequest::MAKS_KESIMPULAN + 1),
            ])
            ->assertSessionHasErrors('kekuatan');

        $this->assertNull($this->peserta->fresh()->penilaian);
    }

    public function test_kesimpulan_dibatasi_jumlah_baris_dan_crlf_dihitung_satu_karakter(): void
    {
        $maksBaris = SimpanPenilaianRequest::MAKS_BARIS_KESIMPULAN;
        $kirim = fn (string $isi) => $this->actingAs($this->user())
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), [
                'nilai' => $this->nilai(85, 82),
                'kekuatan' => $isi,
            ]);

        $kirim(implode("\n", array_fill(0, $maksBaris + 1, 'baris')))->assertSessionHasErrors('kekuatan');

        // Pas batas: baris baru dari browser (CRLF) dihitung 1 karakter, sama seperti penghitung di form.
        $pasBatas = str_repeat('a', SimpanPenilaianRequest::MAKS_KESIMPULAN - 1);
        $kirim(substr($pasBatas, 0, 10)."\r\n".substr($pasBatas, 10))->assertSessionHasNoErrors();
    }

    public function test_penilaian_lama_format_huruf_tetap_tampil(): void
    {
        $penilaian = PenilaianPeserta::create([
            'peserta_magang_id' => $this->peserta->id,
            'nilai_akhir' => 82,
            'predikat' => 'B',
            'catatan_pembimbing' => 'Catatan format lama',
        ]);
        $penilaian->details()->create(['nama_aspek' => 'Kedisiplinan', 'bobot' => 20, 'nilai' => 82]);

        $this->actingAs($this->user())
            ->get(route('superadmin.peserta-magang.penilaian', $this->peserta))
            ->assertOk()
            ->assertSee('Catatan format lama');

        $this->assertSame(['Aspek' => 82.0], array_map(fn ($kelompok) => $kelompok['rata_rata'], $penilaian->nilaiPerKelompok()));
    }

    public function test_tamu_tidak_bisa_menilai(): void
    {
        $this->get(route('superadmin.peserta-magang.penilaian', $this->peserta))->assertRedirect(route('login'));
        $this->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilai(80, 80)])
            ->assertRedirect(route('login'));

        $this->assertSame(0, PenilaianPeserta::count());
    }
}
