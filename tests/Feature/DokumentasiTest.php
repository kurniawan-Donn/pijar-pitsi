<?php

namespace Tests\Feature;

use App\Models\Dokumentasi;
use App\Models\User;
use App\Services\FotoDokumentasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Galeri dokumentasi: dikelola admin & super admin, tampil di beranda.
 */
class DokumentasiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    /**
     * @return array<string, mixed>
     */
    private function isian(array $data = []): array
    {
        return $data + [
            'judul' => 'Pembekalan peserta magang',
            'keterangan' => 'Pengenalan perusahaan dan tata tertib magang.',
            'tanggal_kegiatan' => '2026-08-03',
            'tampil' => '1',
            'foto' => UploadedFile::fake()->image('kegiatan.jpg', 2400, 1600),
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function pengelola(): array
    {
        return ['admin' => [User::ROLE_ADMIN], 'super admin' => [User::ROLE_SUPER_ADMIN]];
    }

    #[DataProvider('pengelola')]
    public function test_admin_dan_super_admin_menambah_dokumentasi_dengan_foto_diperkecil(string $role): void
    {
        $this->actingAs(User::factory()->create(['role' => $role]))
            ->post(route('superadmin.dokumentasi.store'), $this->isian())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $dokumentasi = Dokumentasi::sole();
        $this->assertSame('Pembekalan peserta magang', $dokumentasi->judul);
        $this->assertSame('2026-08-03', $dokumentasi->tanggal_kegiatan->toDateString());
        $this->assertTrue($dokumentasi->tampil);
        $this->assertSame(1, $dokumentasi->urutan);

        [$lebar, $tinggi] = getimagesizefromstring(Storage::disk('public')->get($dokumentasi->foto_path));
        $this->assertSame([FotoDokumentasi::SISI_BESAR, 1067], [$lebar, $tinggi]);

        [$lebar] = getimagesizefromstring(Storage::disk('public')->get($dokumentasi->thumbnail_path));
        $this->assertSame(FotoDokumentasi::SISI_THUMBNAIL, $lebar);

        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'simpan_dokumentasi']);
    }

    public function test_foto_wajib_saat_tambah_dan_harus_gambar(): void
    {
        $this->actingAs($this->admin)
            ->post(route('superadmin.dokumentasi.store'), $this->isian(['foto' => null]))
            ->assertSessionHasErrors(['foto' => 'Pilih foto dokumentasi.']);

        $this->actingAs($this->admin)
            ->post(route('superadmin.dokumentasi.store'), $this->isian(['foto' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')]))
            ->assertSessionHasErrors('foto');

        $this->actingAs($this->admin)
            ->post(route('superadmin.dokumentasi.store'), $this->isian(['judul' => '', 'keterangan' => str_repeat('a', 301), 'tanggal_kegiatan' => now()->addDay()->toDateString()]))
            ->assertSessionHasErrors(['judul', 'keterangan', 'tanggal_kegiatan']);

        $this->assertSame(0, Dokumentasi::count());
    }

    public function test_ubah_tanpa_foto_mempertahankan_foto_lama_dan_ganti_foto_menghapus_yang_lama(): void
    {
        $this->actingAs($this->admin)->post(route('superadmin.dokumentasi.store'), $this->isian());
        $dokumentasi = Dokumentasi::sole();
        $fotoLama = $dokumentasi->foto_path;

        $this->actingAs($this->admin)
            ->put(route('superadmin.dokumentasi.update', $dokumentasi), $this->isian(['judul' => 'Judul baru', 'foto' => null]))
            ->assertSessionHasNoErrors();

        $this->assertSame('Judul baru', $dokumentasi->fresh()->judul);
        $this->assertSame($fotoLama, $dokumentasi->fresh()->foto_path);

        $this->actingAs($this->admin)
            ->put(route('superadmin.dokumentasi.update', $dokumentasi), $this->isian(['foto' => UploadedFile::fake()->image('baru.png', 800, 600)]))
            ->assertSessionHasNoErrors();

        $this->assertNotSame($fotoLama, $dokumentasi->fresh()->foto_path);
        Storage::disk('public')->assertMissing($fotoLama);
        Storage::disk('public')->assertExists($dokumentasi->fresh()->foto_path);
    }

    public function test_hapus_menghapus_foto(): void
    {
        $this->actingAs($this->admin)->post(route('superadmin.dokumentasi.store'), $this->isian());
        $dokumentasi = Dokumentasi::sole();

        $this->actingAs($this->admin)->delete(route('superadmin.dokumentasi.destroy', $dokumentasi))->assertRedirect(route('superadmin.dokumentasi.index'));

        $this->assertModelMissing($dokumentasi);
        Storage::disk('public')->assertMissing($dokumentasi->foto_path);
        Storage::disk('public')->assertMissing($dokumentasi->thumbnail_path);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'hapus_dokumentasi']);
    }

    public function test_urutan_dan_tampil_sembunyikan(): void
    {
        [$satu, $dua, $tiga] = collect(['Satu', 'Dua', 'Tiga'])
            ->map(fn (string $judul, int $i) => Dokumentasi::factory()->create(['judul' => $judul, 'urutan' => $i + 1]))
            ->all();

        $this->actingAs($this->admin)->put(route('superadmin.dokumentasi.urutan', $tiga), ['arah' => 'naik']);
        $this->assertSame(['Satu', 'Tiga', 'Dua'], Dokumentasi::berurutan()->pluck('judul')->all());

        $this->actingAs($this->admin)->put(route('superadmin.dokumentasi.urutan', $satu), ['arah' => 'naik']);
        $this->assertSame(['Satu', 'Tiga', 'Dua'], Dokumentasi::berurutan()->pluck('judul')->all());

        $this->actingAs($this->admin)->put(route('superadmin.dokumentasi.tampil', $dua))->assertSessionHas('success');
        $this->assertFalse($dua->fresh()->tampil);

        $this->actingAs($this->admin)
            ->get(route('superadmin.dokumentasi.index'))
            ->assertOk()
            ->assertSeeInOrder(['Satu', 'Tiga', 'Dua'])
            ->assertSee('Disembunyikan');
    }

    public function test_beranda_menampilkan_galeri_berurutan_hanya_yang_tampil(): void
    {
        Dokumentasi::factory()->create(['judul' => 'Kegiatan Kedua', 'urutan' => 2, 'tanggal_kegiatan' => '2026-08-12', 'keterangan' => 'Presentasi project akhir.']);
        Dokumentasi::factory()->create(['judul' => 'Kegiatan Pertama', 'urutan' => 1]);
        Dokumentasi::factory()->tersembunyi()->create(['judul' => 'Kegiatan Rahasia']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="dokumentasi"', false)
            ->assertSeeInOrder(['Kegiatan Pertama', 'Kegiatan Kedua'])
            ->assertSee('12 Agustus 2026')
            ->assertSee('Presentasi project akhir.')
            ->assertSee('class="galeri-lightbox"', false)
            ->assertDontSee('Kegiatan Rahasia');
    }

    public function test_bagian_dokumentasi_tidak_tampil_bila_kosong(): void
    {
        Dokumentasi::factory()->tersembunyi()->create();

        $this->get(route('home'))->assertOk()->assertDontSee('id="dokumentasi"', false);
    }

    public function test_menu_dokumentasi_di_grup_konten_website(): void
    {
        $this->actingAs($this->admin)
            ->get(route('superadmin.dokumentasi.index'))
            ->assertOk()
            ->assertSeeInOrder(['Konten Website', 'Dokumentasi', 'FAQ'])
            ->assertSee('Belum ada dokumentasi');
    }
}
