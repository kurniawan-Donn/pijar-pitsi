<?php

namespace Tests\Feature;

use App\Models\AspekPenilaian;
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
     * Data form dari aspek yang ada, dengan perubahan opsional.
     *
     * @return array<int, array{nama: string, bobot: int, aktif: int}>
     */
    private function dataAspek(array $ubah = []): array
    {
        $data = AspekPenilaian::orderBy('id')->get()->mapWithKeys(fn (AspekPenilaian $aspek) => [
            $aspek->id => ['nama' => $aspek->nama, 'bobot' => $aspek->bobot, 'aktif' => 1],
        ])->all();

        foreach ($ubah as $id => $nilai) {
            $data[$id] = array_merge($data[$id], $nilai);
        }

        return $data;
    }

    public function test_hanya_super_admin_yang_dapat_mengatur_aspek(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('superadmin.aspek-penilaian.index'))->assertForbidden();
        $this->actingAs($admin)->put(route('superadmin.aspek-penilaian.update'), ['aspek' => $this->dataAspek()])->assertForbidden();

        $this->actingAs($this->superAdmin())->get(route('superadmin.aspek-penilaian.index'))
            ->assertOk()
            ->assertSee('Kemampuan Teknis');
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

    public function test_ubah_bobot_dan_tambah_aspek_baru(): void
    {
        $kedisiplinan = AspekPenilaian::firstWhere('nama', 'Kedisiplinan');
        $komunikasi = AspekPenilaian::firstWhere('nama', 'Komunikasi');

        // Kedisiplinan 20 → 15, Komunikasi dinonaktifkan (−10), aspek baru 15: total tetap 100.
        $this->actingAs($this->superAdmin())
            ->put(route('superadmin.aspek-penilaian.update'), [
                'aspek' => $this->dataAspek([
                    $kedisiplinan->id => ['bobot' => 15],
                    $komunikasi->id => ['aktif' => 0],
                ]),
                'baru' => ['nama' => 'Kreativitas', 'bobot' => 15],
            ])
            ->assertRedirect(route('superadmin.aspek-penilaian.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(15, $kedisiplinan->fresh()->bobot);
        $this->assertFalse($komunikasi->fresh()->aktif);
        $this->assertDatabaseHas('aspek_penilaians', ['nama' => 'Kreativitas', 'bobot' => 15, 'aktif' => true]);
        $this->assertSame(100, (int) AspekPenilaian::aktif()->sum('bobot'));
    }

    public function test_total_bobot_aktif_harus_100(): void
    {
        $kedisiplinan = AspekPenilaian::firstWhere('nama', 'Kedisiplinan');

        $this->actingAs($this->superAdmin())
            ->put(route('superadmin.aspek-penilaian.update'), [
                'aspek' => $this->dataAspek([$kedisiplinan->id => ['bobot' => 30]]),
            ])
            ->assertSessionHasErrors(['total_bobot' => 'Total bobot aspek aktif harus 100%, sekarang 110%.']);

        $this->assertSame(20, $kedisiplinan->fresh()->bobot);
    }
}
