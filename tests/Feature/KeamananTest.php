<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KeamananTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function linkTombolBanner(): array
    {
        return [
            'javascript' => ['javascript:alert(1)', false],
            'javascript huruf besar' => ['JavaScript:alert(1)', false],
            'data uri' => ['data:text/html,<script>alert(1)</script>', false],
            'protocol relative' => ['//situs-lain.test', false],
            'https' => ['https://contoh.test/daftar', true],
            'path internal' => ['/#daftar', true],
            'anchor' => ['#daftar', true],
        ];
    }

    #[DataProvider('linkTombolBanner')]
    public function test_link_tombol_banner_hanya_http_path_atau_anchor(string $link, bool $valid): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->superAdmin())->post(route('superadmin.banners.store'), [
            'judul' => 'Banner Uji',
            'tombol_link' => $link,
        ]);

        $valid
            ? $response->assertSessionHasNoErrors()
            : $response->assertSessionHasErrors('tombol_link');
    }

    public function test_upload_banner_menolak_file_bukan_gambar(): void
    {
        Storage::fake('public');

        $this->actingAs($this->superAdmin())->post(route('superadmin.banners.store'), [
            'judul' => 'Banner Uji',
            'gambar' => UploadedFile::fake()->create('virus.php', 10, 'application/x-php'),
        ])->assertSessionHasErrors('gambar');

        $this->actingAs($this->superAdmin())->post(route('superadmin.banners.store'), [
            'judul' => 'Banner Uji',
            'gambar' => UploadedFile::fake()->create('gambar.svg', 10, 'image/svg+xml'),
        ])->assertSessionHasErrors('gambar');

        $this->actingAs($this->superAdmin())->post(route('superadmin.banners.store'), [
            'judul' => 'Banner Uji',
            'gambar' => UploadedFile::fake()->image('banner.jpg')->size(3000),
        ])->assertSessionHasErrors(['gambar' => 'Ukuran gambar banner maksimal 2 MB.']);
    }

    public function test_link_google_sheets_peserta_harus_spreadsheet_google(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('superadmin.peserta-magang.store'), [
            'nama' => 'Peserta',
            'status' => 'aktif',
            'google_sheet_url' => 'https://situs-lain.test/spreadsheets/d/abc',
        ])->assertSessionHasErrors('google_sheet_url');

        $this->actingAs($admin)->post(route('superadmin.peserta-magang.store'), [
            'nama' => 'Peserta',
            'status' => 'aktif',
            'google_sheet_url' => 'https://docs.google.com/spreadsheets/d/abc123/edit',
        ])->assertSessionHasNoErrors();
    }

    public function test_super_admin_tidak_bisa_menurunkan_role_sendiri(): void
    {
        $superAdmin = $this->superAdmin();
        $this->superAdmin(); // masih ada super admin lain

        $this->actingAs($superAdmin)
            ->put(route('superadmin.pengguna.update', $superAdmin), [
                'name' => $superAdmin->name,
                'email' => $superAdmin->email,
                'role' => 'admin',
            ])
            ->assertSessionHas('error', 'Role akun yang sedang digunakan tidak dapat diturunkan.');

        $this->assertSame('super_admin', $superAdmin->fresh()->role);
    }

    public function test_super_admin_terakhir_tidak_bisa_diturunkan_atau_dihapus(): void
    {
        // Pelaku adalah super admin yang sudah dihapus dari tabel (tidak dihitung),
        // sehingga target menjadi super admin terakhir.
        $pelaku = $this->superAdmin();
        $terakhir = $this->superAdmin();
        $pelaku->forceFill(['role' => 'admin'])->saveQuietly();
        $pelaku->role = 'super_admin'; // sesi pelaku masih tercatat super admin

        $this->actingAs($pelaku)
            ->put(route('superadmin.pengguna.update', $terakhir), [
                'name' => $terakhir->name,
                'email' => $terakhir->email,
                'role' => 'admin',
            ])
            ->assertSessionHas('error');

        $this->actingAs($pelaku)
            ->delete(route('superadmin.pengguna.destroy', $terakhir))
            ->assertSessionHas('error', 'Super Admin terakhir tidak dapat dihapus.');

        $this->assertSame('super_admin', $terakhir->fresh()->role);
    }

    public function test_super_admin_bisa_menurunkan_super_admin_lain_bila_masih_ada_yang_lain(): void
    {
        $saya = $this->superAdmin();
        $lain = $this->superAdmin();

        $this->actingAs($saya)
            ->put(route('superadmin.pengguna.update', $lain), [
                'name' => $lain->name,
                'email' => $lain->email,
                'role' => 'admin',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionMissing('error');

        $this->assertSame('admin', $lain->fresh()->role);
    }

    public function test_password_pengguna_baru_wajib_huruf_dan_angka(): void
    {
        $this->actingAs($this->superAdmin())
            ->post(route('superadmin.pengguna.store'), [
                'name' => 'Admin Baru',
                'email' => 'baru@contoh.test',
                'password' => 'hanyahuruf',
                'password_confirmation' => 'hanyahuruf',
                'role' => 'admin',
            ])
            ->assertSessionHasErrors(['password' => 'Password harus berisi minimal satu angka.']);
    }

    public function test_halaman_detail_pengguna_tidak_lagi_error(): void
    {
        $this->actingAs($this->superAdmin())
            ->get('/superadmin/pengguna/1')
            ->assertStatus(405);
    }
}
