<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Divisi;
use App\Models\HariLibur;
use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\PengajuanMagang;
use App\Models\PesertaMagang;
use App\Models\TarifUangSaku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Setiap route aplikasi punya minimal satu test akses untuk tamu, admin,
 * super admin, dan peserta. Route baru yang belum didaftarkan di matriks membuat
 * test_semua_route_aplikasi_tercakup gagal.
 */
class AksesRouteTest extends TestCase
{
    use RefreshDatabase;

    private const PUBLIK = 'publik';

    private const SEMUA = 'semua';            // semua pengguna login

    private const LOGIN = 'login';            // admin & super admin

    private const ADMIN = 'admin';            // hanya admin

    private const SUPER_ADMIN = 'super_admin'; // hanya super admin

    private const PESERTA = 'peserta';        // hanya peserta magang

    /**
     * [nama route => siapa yang boleh mengakses]
     *
     * @var array<string, string>
     */
    private const MATRIKS = [
        // Publik
        'home' => self::PUBLIK,
        'login' => self::PUBLIK,
        'login.process' => self::PUBLIK,
        'pengajuan.store' => self::PUBLIK,
        'pengajuan.status' => self::PUBLIK,

        // Semua pengguna login (termasuk peserta)
        'logout' => self::SEMUA,
        'profil.edit' => self::SEMUA,
        'profil.update' => self::SEMUA,
        'profil.password' => self::SEMUA,

        // Khusus peserta
        'peserta.dashboard' => self::PESERTA,
        'peserta.biodata.edit' => self::PESERTA,
        'peserta.biodata.update' => self::PESERTA,
        'peserta.logbook.index' => self::PESERTA,
        'peserta.logbook.cetak' => self::PESERTA,
        'peserta.logbook.bukti' => self::PESERTA,
        'peserta.logbook.edit' => self::PESERTA,
        'peserta.logbook.update' => self::PESERTA,
        'peserta.logbook.destroy' => self::PESERTA,
        'peserta.feedback.edit' => self::PESERTA,
        'peserta.feedback.update' => self::PESERTA,
        'peserta.surat.show' => self::PESERTA,
        'peserta.surat.unduh' => self::PESERTA,
        'peserta.izin.create' => self::PESERTA,
        'peserta.izin.store' => self::PESERTA,
        'peserta.izin.destroy' => self::PESERTA,
        'peserta.izin.bukti' => self::PESERTA,

        // Khusus admin
        'admin.dashboard' => self::ADMIN,

        // Admin + super admin
        'admin.pengajuan.index' => self::LOGIN,
        'admin.pengajuan.create' => self::LOGIN,
        'admin.pengajuan.show' => self::LOGIN,
        'admin.pengajuan.dokumen' => self::LOGIN,
        'admin.pengajuan.status' => self::LOGIN,
        'admin.pengajuan.peserta' => self::LOGIN,
        'admin.pengajuan.destroy' => self::LOGIN,
        'superadmin.perusahaan.index' => self::LOGIN,
        'superadmin.perusahaan.update' => self::LOGIN,
        'superadmin.banners.index' => self::LOGIN,
        'superadmin.banners.create' => self::LOGIN,
        'superadmin.banners.store' => self::LOGIN,
        'superadmin.banners.show' => self::LOGIN,
        'superadmin.banners.edit' => self::LOGIN,
        'superadmin.banners.update' => self::LOGIN,
        'superadmin.banners.destroy' => self::LOGIN,
        'superadmin.pengaturan.index' => self::LOGIN,
        'superadmin.pengaturan.update' => self::LOGIN,
        'superadmin.peserta-magang.index' => self::LOGIN,
        'superadmin.peserta-magang.create' => self::LOGIN,
        'superadmin.peserta-magang.store' => self::LOGIN,
        'superadmin.peserta-magang.show' => self::LOGIN,
        'superadmin.peserta-magang.edit' => self::LOGIN,
        'superadmin.peserta-magang.update' => self::LOGIN,
        'superadmin.peserta-magang.destroy' => self::LOGIN,
        'superadmin.pengaturan-magang.index' => self::LOGIN,
        'superadmin.pengaturan-magang.update' => self::LOGIN,
        'superadmin.pengaturan-magang.tarif.store' => self::LOGIN,
        'superadmin.pengaturan-magang.tarif.destroy' => self::LOGIN,
        'superadmin.pengaturan-magang.libur.store' => self::LOGIN,
        'superadmin.pengaturan-magang.libur.destroy' => self::LOGIN,
        'superadmin.divisi.index' => self::LOGIN,
        'superadmin.divisi.store' => self::LOGIN,
        'superadmin.divisi.update' => self::LOGIN,
        'superadmin.divisi.destroy' => self::LOGIN,
        'superadmin.peserta-magang.penempatan.update' => self::LOGIN,
        'superadmin.peserta-magang.biodata' => self::LOGIN,
        'superadmin.peserta-magang.biodata.program' => self::LOGIN,
        'superadmin.peserta-magang.biodata.tinjau' => self::LOGIN,
        'superadmin.peserta-magang.logbook-harian' => self::LOGIN,
        'superadmin.peserta-magang.logbook-harian.verifikasi' => self::LOGIN,
        'superadmin.peserta-magang.logbook-harian.buka-kunci' => self::LOGIN,
        'superadmin.peserta-magang.logbook-harian.cetak' => self::LOGIN,
        'superadmin.peserta-magang.izin.verifikasi' => self::LOGIN,
        'superadmin.logbook-bukti.show' => self::LOGIN,
        'superadmin.izin-bukti.show' => self::LOGIN,
        'superadmin.peserta-magang.impor-logbook.sheets' => self::LOGIN,
        'superadmin.peserta-magang.impor-logbook.xlsx' => self::LOGIN,
        'superadmin.peserta-magang.impor-logbook.hapus' => self::LOGIN,
        'superadmin.peserta-magang.akun.store' => self::LOGIN,
        'superadmin.peserta-magang.akun.reset' => self::LOGIN,
        'superadmin.peserta-magang.akun.status' => self::LOGIN,
        'superadmin.peserta-magang.akun.destroy' => self::LOGIN,
        'superadmin.peserta-magang.penilaian' => self::LOGIN,
        'superadmin.peserta-magang.penilaian.update' => self::LOGIN,
        'superadmin.peserta-magang.penilaian.cetak' => self::LOGIN,
        'superadmin.monitoring.index' => self::LOGIN,
        'superadmin.peserta-magang.surat' => self::LOGIN,
        'superadmin.peserta-magang.surat.draft' => self::LOGIN,
        'superadmin.peserta-magang.surat.terbitkan' => self::LOGIN,
        'superadmin.peserta-magang.surat.pdf' => self::LOGIN,
        'superadmin.feedback.index' => self::LOGIN,
        'superadmin.peserta-magang.feedback' => self::LOGIN,
        'superadmin.laporan.index' => self::LOGIN,
        'superadmin.laporan.cetak' => self::LOGIN,
        'superadmin.laporan.csv' => self::LOGIN,
        'superadmin.laporan.xlsx' => self::LOGIN,
        'superadmin.laporan.pdf' => self::LOGIN,

        // Khusus super admin
        'superadmin.dashboard' => self::SUPER_ADMIN,
        'superadmin.pengguna.index' => self::SUPER_ADMIN,
        'superadmin.pengguna.create' => self::SUPER_ADMIN,
        'superadmin.pengguna.store' => self::SUPER_ADMIN,
        'superadmin.pengguna.edit' => self::SUPER_ADMIN,
        'superadmin.pengguna.update' => self::SUPER_ADMIN,
        'superadmin.pengguna.destroy' => self::SUPER_ADMIN,
        'superadmin.aspek-penilaian.index' => self::SUPER_ADMIN,
        'superadmin.aspek-penilaian.update' => self::SUPER_ADMIN,
        'superadmin.aspek-penilaian.predikat' => self::SUPER_ADMIN,
        'superadmin.log-aktivitas.index' => self::SUPER_ADMIN,
    ];

    /**
     * Route bawaan framework / paket yang bukan bagian aplikasi.
     */
    private const DIABAIKAN = ['storage.local', 'storage.local.upload'];

    public function test_semua_route_aplikasi_tercakup(): void
    {
        $route = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $r) => $r->getName() && ! in_array($r->getName(), self::DIABAIKAN, true))
            ->filter(fn (Route $r) => str_starts_with($r->getActionName(), 'App\\'))
            ->map(fn (Route $r) => $r->getName())
            ->values()
            ->all();

        $belumTercakup = array_diff($route, array_keys(self::MATRIKS));
        $sudahHilang = array_diff(array_keys(self::MATRIKS), $route);

        $this->assertSame([], array_values($belumTercakup), 'Route baru belum punya test akses: '.implode(', ', $belumTercakup));
        $this->assertSame([], array_values($sudahHilang), 'Route di matriks sudah tidak ada: '.implode(', ', $sudahHilang));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function semuaRoute(): array
    {
        return collect(self::MATRIKS)
            ->mapWithKeys(fn (string $akses, string $nama) => [$nama => [$nama, $akses]])
            ->all();
    }

    #[DataProvider('semuaRoute')]
    public function test_akses_route_untuk_tamu_admin_super_admin_dan_peserta(string $nama, string $akses): void
    {
        $route = RouteFacade::getRoutes()->getByName($nama);
        $this->assertNotNull($route, "Route {$nama} tidak ditemukan");

        $method = collect($route->methods())->reject(fn ($m) => $m === 'HEAD')->first();

        Storage::fake('local');

        foreach ([null, 'admin', 'super_admin', 'peserta'] as $role) {
            $this->refreshApplicationState();

            $login = $role ? $this->pengguna($role) : null;

            // Dibuat ulang per role: route hapus menghapus datanya.
            $parameter = $this->parameter($route, $login);

            if ($login) {
                $this->actingAs($login);
            }

            $response = $this->call($method, route($nama, $parameter));
            $label = "{$nama} sebagai ".($role ?? 'tamu');

            $boleh = match ($akses) {
                self::PUBLIK => true,
                self::SEMUA => $role !== null,
                self::LOGIN => in_array($role, ['admin', 'super_admin'], true),
                self::ADMIN => $role === 'admin',
                self::SUPER_ADMIN => $role === 'super_admin',
                self::PESERTA => $role === 'peserta',
            };

            if ($boleh) {
                $this->assertNotContains($response->getStatusCode(), [401, 403, 404, 405, 500], "{$label} seharusnya boleh, dapat {$response->getStatusCode()}");

                // Logout yang berhasil memang diarahkan ke halaman login.
                if ($akses !== self::PUBLIK && $nama !== 'logout') {
                    $this->assertFalse($response->isRedirect(route('login')), "{$label} seharusnya tidak diarahkan ke login");
                }
            } elseif ($role === null) {
                $response->assertRedirect(route('login'));
            } else {
                $this->assertSame(403, $response->getStatusCode(), "{$label} seharusnya ditolak (403)");
            }
        }
    }

    /**
     * Akun untuk role yang diuji. Akun peserta selalu terhubung ke data peserta.
     */
    private function pengguna(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);

        if ($role === 'peserta') {
            PesertaMagang::create($this->dataPeserta(['nama' => 'Peserta Login', 'user_id' => $user->id]));
        }

        return $user;
    }

    /**
     * Peserta aktif dengan masa magang yang mencakup hari ini.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function dataPeserta(array $data = []): array
    {
        return $data + [
            'nama' => 'Peserta Akses',
            'status' => 'aktif',
            'periode_mulai' => now()->subDays(30)->toDateString(),
            'periode_selesai' => now()->addDays(30)->toDateString(),
        ];
    }

    /**
     * Logout memutus sesi, jadi tiap role diuji dengan pengguna baru.
     */
    private function refreshApplicationState(): void
    {
        auth()->forgetGuards();
        $this->flushSession();
    }

    /**
     * Data contoh untuk parameter route.
     *
     * @return array<string, mixed>
     */
    private function parameter(Route $route, ?User $login): array
    {
        $isi = [];

        // Data milik peserta yang login (untuk route peserta), atau peserta baru.
        $peserta = $login?->pesertaMagang;

        foreach ($route->parameterNames() as $nama) {
            $isi[$nama] = match ($nama) {
                'pengajuanMagang' => tap(
                    PengajuanMagang::factory()->denganDokumen('pengajuan/akses.pdf')->create(),
                    fn () => Storage::disk('local')->put('pengajuan/akses.pdf', '%PDF-uji')
                ),
                'pesertaMagang' => $peserta = PesertaMagang::create($this->dataPeserta()),
                'banner' => Banner::create(['judul' => 'Banner Akses', 'status' => 'aktif']),
                'user' => User::factory()->create(['role' => 'admin']),
                'tarifUangSaku' => TarifUangSaku::create(['berlaku_mulai' => fake()->unique()->date(), 'tarif_hari_penuh' => 60000, 'tarif_setengah_hari' => 30000]),
                'hariLibur' => HariLibur::create(['tanggal' => fake()->unique()->date(), 'keterangan' => 'Libur Akses']),
                'divisi' => Divisi::create(['nama' => fake()->unique()->words(3, true)]),
                'tanggal' => now()->toDateString(),
                'logbookBukti' => tap(
                    LogbookHarian::create([
                        'peserta_magang_id' => ($peserta ??= PesertaMagang::create($this->dataPeserta()))->id,
                        'tanggal' => now()->subDays(fake()->unique()->numberBetween(1, 25))->toDateString(),
                        'kehadiran' => 'hadir',
                    ])->buktis()->create(['path' => 'logbook/akses.pdf', 'nama_asli' => 'akses.pdf', 'mime' => 'application/pdf', 'ukuran' => 8]),
                    fn () => Storage::disk('local')->put('logbook/akses.pdf', '%PDF-uji')
                ),
                'izinPeserta' => tap(
                    IzinPeserta::create([
                        'peserta_magang_id' => ($peserta ??= PesertaMagang::create($this->dataPeserta()))->id,
                        'jenis' => 'sakit',
                        'tanggal_mulai' => now()->toDateString(),
                        'tanggal_selesai' => now()->toDateString(),
                        'keterangan' => 'Sakit akses',
                        'bukti_path' => 'izin/akses.pdf',
                        'bukti_nama' => 'akses.pdf',
                    ]),
                    fn () => Storage::disk('local')->put('izin/akses.pdf', '%PDF-uji')
                ),
            };
        }

        return $isi;
    }
}
