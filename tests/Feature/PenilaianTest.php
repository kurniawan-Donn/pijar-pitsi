<?php

namespace Tests\Feature;

use App\Models\AspekPenilaian;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PenilaianTest extends TestCase
{
    use RefreshDatabase;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->peserta = PesertaMagang::create(['nama' => 'Peserta Dinilai', 'status' => 'aktif']);
    }

    private function user(string $role = 'admin'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    /**
     * Nilai yang sama untuk semua aspek aktif.
     *
     * @return array<int, int>
     */
    private function nilaiSemua(int $nilai): array
    {
        return AspekPenilaian::aktif()->pluck('id')->mapWithKeys(fn ($id) => [$id => $nilai])->all();
    }

    public function test_aspek_bawaan_tersedia_dengan_total_bobot_100(): void
    {
        $this->assertSame(6, AspekPenilaian::aktif()->count());
        $this->assertSame(100, (int) AspekPenilaian::aktif()->sum('bobot'));
    }

    /**
     * @return array<string, array{float, string}>
     */
    public static function contohPredikat(): array
    {
        return [
            '85 = A' => [85.0, 'A'],
            '84,99 = B' => [84.99, 'B'],
            '75 = B' => [75.0, 'B'],
            '65 = C' => [65.0, 'C'],
            '50 = D' => [50.0, 'D'],
            '49,9 = E' => [49.9, 'E'],
        ];
    }

    #[DataProvider('contohPredikat')]
    public function test_predikat_sesuai_batas(float $nilai, string $predikat): void
    {
        $this->assertSame($predikat, PenilaianPeserta::predikatUntuk($nilai));
    }

    public function test_nilai_akhir_dihitung_berbobot(): void
    {
        $this->assertSame(82.5, PenilaianPeserta::hitungNilaiAkhir([
            ['nilai' => 90, 'bobot' => 50],
            ['nilai' => 75, 'bobot' => 50],
        ]));

        $this->assertSame(0.0, PenilaianPeserta::hitungNilaiAkhir([]));
    }

    #[DataProvider('rolePengelola')]
    public function test_admin_dan_super_admin_dapat_mengisi_penilaian(string $role): void
    {
        $user = $this->user($role);
        $aspek = AspekPenilaian::aktif()->get();

        // Kemampuan Teknis (bobot 25) = 100, aspek lain = 80 → (75×80 + 25×100) / 100 = 85
        $nilai = $this->nilaiSemua(80);
        $nilai[$aspek->firstWhere('nama', 'Kemampuan Teknis')->id] = 100;

        $this->actingAs($user)->get(route('superadmin.peserta-magang.penilaian', $this->peserta))->assertOk();

        $this->actingAs($user)
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), [
                'nilai' => $nilai,
                'catatan_pembimbing' => 'Sangat baik.',
            ])
            ->assertRedirect(route('superadmin.peserta-magang.penilaian', $this->peserta))
            ->assertSessionHas('success');

        $penilaian = $this->peserta->penilaian()->with('details')->first();

        $this->assertSame(85.0, $penilaian->nilai_akhir);
        $this->assertSame('A', $penilaian->predikat);
        $this->assertSame($user->id, $penilaian->dinilai_oleh);
        $this->assertCount(6, $penilaian->details);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function rolePengelola(): array
    {
        return ['admin' => ['admin'], 'super admin' => ['super_admin']];
    }

    public function test_menyimpan_ulang_mengganti_nilai_lama(): void
    {
        $user = $this->user();

        $this->actingAs($user)->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilaiSemua(70)]);
        $this->actingAs($user)->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilaiSemua(90)]);

        $this->assertSame(1, PenilaianPeserta::count());
        $this->assertSame(90.0, $this->peserta->penilaian()->first()->nilai_akhir);
        $this->assertSame(6, $this->peserta->penilaian()->first()->details()->count());
    }

    public function test_semua_aspek_wajib_dinilai_dengan_nilai_0_sampai_100(): void
    {
        $nilai = $this->nilaiSemua(80);
        $idPertama = array_key_first($nilai);
        $idKedua = array_keys($nilai)[1];

        unset($nilai[$idPertama]);
        $nilai[$idKedua] = 120;

        $this->actingAs($this->user())
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $nilai])
            ->assertSessionHasErrors(["nilai.{$idPertama}", "nilai.{$idKedua}"]);

        $this->assertSame(0, PenilaianPeserta::count());
    }

    public function test_nilai_lama_tidak_berubah_saat_bobot_aspek_diubah(): void
    {
        $this->actingAs($this->user())
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilaiSemua(80)]);

        AspekPenilaian::query()->update(['nama' => 'Diubah', 'bobot' => 1]);

        $detail = $this->peserta->penilaian()->first()->details()->first();

        $this->assertSame('Kedisiplinan', $detail->nama_aspek);
        $this->assertSame(20, $detail->bobot);
        $this->assertSame(80.0, $this->peserta->penilaian()->first()->nilai_akhir);
    }

    public function test_nilai_tampil_di_daftar_dan_detail_peserta(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilaiSemua(78)]);

        $this->actingAs($user)->get(route('superadmin.peserta-magang.index'))
            ->assertSee('78,00')
            ->assertSee('(B)');

        $this->actingAs($user)->get(route('superadmin.peserta-magang.show', $this->peserta))
            ->assertSee('Nilai 78,00')
            ->assertSee('predikat B');
    }

    public function test_tamu_tidak_bisa_menilai(): void
    {
        $this->get(route('superadmin.peserta-magang.penilaian', $this->peserta))->assertRedirect(route('login'));
        $this->put(route('superadmin.peserta-magang.penilaian.update', $this->peserta), ['nilai' => $this->nilaiSemua(80)])
            ->assertRedirect(route('login'));

        $this->assertSame(0, PenilaianPeserta::count());
    }
}
