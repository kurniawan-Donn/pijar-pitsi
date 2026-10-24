<?php

namespace Tests\Feature;

use App\Models\AspekPenilaian;
use App\Models\KelompokPenilaian;
use App\Models\PredikatPenilaian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AspekPenilaianTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    /**
     * Data form kelompok & aspek yang ada, dengan perubahan opsional.
     *
     * @param  array<int, array<string, mixed>>  $kelompok  [id => perubahan]
     * @param  array<int, array<string, mixed>>  $aspek  [id => perubahan]
     * @return array<string, mixed>
     */
    private function dataForm(array $kelompok = [], array $aspek = []): array
    {
        return [
            'kelompok' => KelompokPenilaian::orderBy('id')->get()->mapWithKeys(fn (KelompokPenilaian $item) => [
                $item->id => array_merge(['nama' => $item->nama, 'bobot' => $item->bobot], $kelompok[$item->id] ?? []),
            ])->all(),
            'aspek' => AspekPenilaian::whereNotNull('kelompok_penilaian_id')->orderBy('id')->get()->mapWithKeys(fn (AspekPenilaian $item) => [
                $item->id => array_merge(['nama' => $item->nama, 'aktif' => 1], $aspek[$item->id] ?? []),
            ])->all(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function dataPredikat(): array
    {
        return PredikatPenilaian::orderBy('batas_bawah')->get()->mapWithKeys(fn (PredikatPenilaian $item) => [
            $item->id => ['batas_bawah' => $item->batas_bawah, 'nama' => $item->nama, 'keterangan' => $item->keterangan, 'hapus' => 0],
        ])->all();
    }

    public function test_hanya_super_admin_yang_dapat_mengatur_penilaian(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('superadmin.aspek-penilaian.index'))->assertForbidden();
        $this->actingAs($admin)->put(route('superadmin.aspek-penilaian.update'), $this->dataForm())->assertForbidden();
        $this->actingAs($admin)->put(route('superadmin.aspek-penilaian.predikat'), ['predikat' => $this->dataPredikat()])->assertForbidden();

        $this->actingAs($this->superAdmin())->get(route('superadmin.aspek-penilaian.index'))
            ->assertOk()
            ->assertSee('Hard Skill')
            ->assertSee('Kolaborasi Tim')
            ->assertSee('Sangat Memuaskan');
    }

    public function test_menu_aspek_hanya_tampil_untuk_super_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.dashboard'))
            ->assertDontSee(route('superadmin.aspek-penilaian.index'));

        $this->actingAs($this->superAdmin())
            ->get(route('superadmin.dashboard'))
            ->assertSee(route('superadmin.aspek-penilaian.index'));
    }

    public function test_ubah_bobot_kelompok_nonaktifkan_dan_tambah_aspek(): void
    {
        $hard = KelompokPenilaian::firstWhere('nama', 'Hard Skill');
        $soft = KelompokPenilaian::firstWhere('nama', 'Soft Skill');
        $kejujuran = AspekPenilaian::firstWhere('nama', 'Kejujuran');

        $this->actingAs($this->superAdmin())
            ->put(route('superadmin.aspek-penilaian.update'), $this->dataForm(
                [$hard->id => ['bobot' => 70], $soft->id => ['bobot' => 30]],
                [$kejujuran->id => ['aktif' => 0]],
            ) + ['baru' => [$soft->id => 'Kreativitas']])
            ->assertRedirect(route('superadmin.aspek-penilaian.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(70, $hard->fresh()->bobot);
        $this->assertFalse($kejujuran->fresh()->aktif);
        $this->assertDatabaseHas('aspek_penilaians', ['nama' => 'Kreativitas', 'kelompok_penilaian_id' => $soft->id, 'aktif' => true]);
        $this->assertSame(8, $soft->aspekAktif()->count());
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'ubah_aspek_penilaian']);
    }

    public function test_total_bobot_kelompok_harus_100(): void
    {
        $hard = KelompokPenilaian::firstWhere('nama', 'Hard Skill');

        $this->actingAs($this->superAdmin())
            ->put(route('superadmin.aspek-penilaian.update'), $this->dataForm([$hard->id => ['bobot' => 70]]))
            ->assertSessionHasErrors(['total_bobot' => 'Total bobot kelompok harus 100%, sekarang 110%.']);

        $this->assertSame(60, $hard->fresh()->bobot);
    }

    public function test_kelompok_baru(): void
    {
        $hard = KelompokPenilaian::firstWhere('nama', 'Hard Skill');

        $this->actingAs($this->superAdmin())
            ->put(route('superadmin.aspek-penilaian.update'), $this->dataForm([$hard->id => ['bobot' => 50]]) + [
                'kelompok_baru' => ['nama' => 'Proyek Akhir', 'bobot' => 10],
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('kelompok_penilaians', ['nama' => 'Proyek Akhir', 'bobot' => 10]);
    }

    public function test_ubah_tabel_predikat(): void
    {
        $data = $this->dataPredikat();
        $sangat = PredikatPenilaian::firstWhere('nama', 'Sangat Memuaskan');
        $data[$sangat->id]['batas_bawah'] = 85;

        $this->actingAs($this->superAdmin())
            ->put(route('superadmin.aspek-penilaian.predikat'), ['predikat' => $data, 'baru' => ['batas_bawah' => 95, 'nama' => 'Istimewa']])
            ->assertSessionHas('success');

        $this->assertSame('Memuaskan', PredikatPenilaian::untuk(84)->nama);
        $this->assertSame('Sangat Memuaskan', PredikatPenilaian::untuk(90)->nama);
        $this->assertSame('Istimewa', PredikatPenilaian::untuk(96)->nama);
    }

    public function test_predikat_wajib_punya_batas_nol_dan_tidak_kembar(): void
    {
        $data = $this->dataPredikat();
        $terbawah = PredikatPenilaian::orderBy('batas_bawah')->first();

        $hapusTerbawah = $data;
        $hapusTerbawah[$terbawah->id]['hapus'] = 1;

        $this->actingAs($this->superAdmin())
            ->put(route('superadmin.aspek-penilaian.predikat'), ['predikat' => $hapusTerbawah])
            ->assertSessionHasErrorsIn('predikat', ['predikat' => 'Harus ada predikat dengan batas bawah 0 supaya semua nilai punya predikat.']);

        $this->actingAs($this->superAdmin())
            ->put(route('superadmin.aspek-penilaian.predikat'), ['predikat' => $data, 'baru' => ['batas_bawah' => 40.01, 'nama' => 'Kembar']])
            ->assertSessionHasErrorsIn('predikat', ['predikat' => 'Batas bawah predikat tidak boleh sama.']);

        $this->assertSame(5, PredikatPenilaian::count());
    }
}
