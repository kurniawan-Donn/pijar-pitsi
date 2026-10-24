<?php

namespace Tests\Feature;

use App\Models\PesertaMagang;
use App\Models\User;
use App\Services\GoogleSheetsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Logbook dibaca dari Google Sheets (di-mock) dan diverifikasi lewat kolom Action.
 */
class LogbookTest extends TestCase
{
    use RefreshDatabase;

    private const URL = 'https://docs.google.com/spreadsheets/d/abc123/edit';

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 09:00:00');

        $this->peserta = PesertaMagang::create([
            'nama' => 'Peserta Logbook',
            'status' => 'aktif',
            'google_sheet_url' => self::URL,
        ]);
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function isiSheet(): array
    {
        $isi = [
            ['No', 'Tanggal', 'Pagi', '', 'Siang', '', 'Activity', 'Output', 'Lokasi', 'Action'],
            ['', '', 'Masuk', 'Keluar', 'Masuk', 'Keluar'],
            ['1', '01/10/2026', '08:00', '12:00', '13:00', '16:00', 'Membuat halaman login', 'Fitur login', 'Kantor', 'Diterima'],
            ['2', '02/10/2026', '08:00', '12:00', '', '', 'Rapat sprint', 'Notulen', 'Kantor', 'review'],
            ['3', '03/10/2026', '08:00', '12:00', '', '', '', '', '', '......'],
            ['4', '04/10/2026', '', '', '', '', 'Memperbaiki bug', '', 'Rumah', '......'],
        ];

        for ($no = 5; $no <= 31; $no++) {
            $isi[] = [(string) $no, '', '', '', '', '', '', '', '', '......'];
        }

        return $isi;
    }

    private function user(string $role = 'admin'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function rolePengelola(): array
    {
        return ['admin' => ['admin'], 'super admin' => ['super_admin']];
    }

    #[DataProvider('rolePengelola')]
    public function test_logbook_menampilkan_hari_berisi_kegiatan_dan_ringkasan(string $role): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andReturn(['OKTOBER 2026']);
            $mock->shouldReceive('getValuesFromUrl')->andReturn($this->isiSheet());
        });

        $response = $this->actingAs($this->user($role))
            ->get(route('superadmin.peserta-magang.logbook', $this->peserta));

        $response->assertOk()
            ->assertSee('Membuat halaman login')
            ->assertSee('Memperbaiki bug')
            ->assertSee('Belum diperiksa');

        // Baris 15 (tanggal 3) hanya berisi jam, bukan logbook.
        $this->assertSame([13, 14, 16], array_column($response->viewData('logbook'), 'sheet_row'));
        $this->assertSame(
            ['total' => 3, 'belum' => 1, 'Diterima' => 1, 'Ditolak' => 0, 'Review' => 1],
            $response->viewData('ringkasan')
        );
    }

    public function test_tamu_tidak_bisa_membuka_logbook(): void
    {
        $this->get(route('superadmin.peserta-magang.logbook', $this->peserta))
            ->assertRedirect(route('login'));

        $this->put(route('superadmin.peserta-magang.logbook.update', $this->peserta), [])
            ->assertRedirect(route('login'));
    }

    public function test_verifikasi_menulis_action_ke_baris_terpilih(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andReturn(['OKTOBER 2026']);
            $mock->shouldReceive('batchUpdateValuesFromUrl')
                ->once()
                ->with(self::URL, [
                    "'OKTOBER 2026'!K14" => [['Diterima']],
                    "'OKTOBER 2026'!K16" => [['Diterima']],
                ])
                ->andReturn(2);
        });

        $this->actingAs($this->user())
            ->put(route('superadmin.peserta-magang.logbook.update', $this->peserta), [
                'bulan' => '2026-10',
                'action' => 'Diterima',
                'baris' => [14, 16],
            ])
            ->assertRedirect(route('superadmin.peserta-magang.logbook', ['pesertaMagang' => $this->peserta, 'bulan' => '2026-10']))
            ->assertSessionHas('success', '2 hari logbook ditandai "Diterima".');
    }

    public function test_verifikasi_memvalidasi_action_dan_baris(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('batchUpdateValuesFromUrl');
        });

        $this->actingAs($this->user())
            ->put(route('superadmin.peserta-magang.logbook.update', $this->peserta), [
                'bulan' => '2026-10',
                'action' => 'Lulus',
                'baris' => [5, 99],
            ])
            ->assertSessionHasErrors(['action', 'baris.0', 'baris.1']);

        $this->actingAs($this->user())
            ->put(route('superadmin.peserta-magang.logbook.update', $this->peserta), [
                'bulan' => '2026-10',
                'action' => 'Ditolak',
            ])
            ->assertSessionHasErrors(['baris' => 'Pilih minimal satu hari logbook.']);
    }

    public function test_verifikasi_gagal_menampilkan_pesan(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andReturn(['OKTOBER 2026']);
            $mock->shouldReceive('batchUpdateValuesFromUrl')->andThrow(new \RuntimeException('403'));
        });

        $this->actingAs($this->user())
            ->put(route('superadmin.peserta-magang.logbook.update', $this->peserta), [
                'bulan' => '2026-10',
                'action' => 'Review',
                'baris' => [13],
            ])
            ->assertSessionHas('error');
    }
}
