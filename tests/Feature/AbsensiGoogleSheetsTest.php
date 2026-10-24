<?php

namespace Tests\Feature;

use App\Models\PesertaMagang;
use App\Models\User;
use App\Services\GoogleSheetsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Absensi Google Sheets dengan GoogleSheetsService di-mock (tanpa internet).
 */
class AbsensiGoogleSheetsTest extends TestCase
{
    use RefreshDatabase;

    private const URL = 'https://docs.google.com/spreadsheets/d/abc123/edit';

    private User $admin;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 09:00:00');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->peserta = PesertaMagang::create([
            'nama' => 'Peserta Absensi',
            'status' => 'aktif',
            'google_sheet_url' => self::URL,
            'periode_mulai' => '2026-09-01',
            'periode_selesai' => '2026-12-31',
        ]);
    }

    /**
     * Isi mentah sheet mulai baris 11 (header), 12 (sub header), 13 (data).
     *
     * @return array<int, array<int, string>>
     */
    private function isiSheet(): array
    {
        $isi = [
            ['No', 'Tanggal', 'Pagi', '', 'Siang', '', 'Activity', 'Output', 'Lokasi', 'Action'],
            ['', '', 'Masuk', 'Keluar', 'Masuk', 'Keluar'],
            ['1', '01/10/2026', '08:00', '12:00', '13:00', '16.00', 'Membuat laporan', 'Laporan', 'Kantor', 'Diterima'],
            ['2', '', '', '', '', '', '', '', '', '......'],
            ['3', '03/10/2026', '', '', '', '', 'Izin acara kampus', '', '', '......'],
            ['4', '04/10/2026', '', '', '', '', 'Sakit', '', '', 'Review'],
            ['5', '05/10/2026', '08:00', '12:00', '', '', 'Rapat tim', 'Notulen', 'Kantor', 'Ditolak'],
        ];

        // Baris 6–31 template: hanya nomor & placeholder Action.
        for ($no = 6; $no <= 31; $no++) {
            $isi[] = [(string) $no, '', '', '', '', '', '', '', '', '......'];
        }

        return $isi;
    }

    private function bukaDetail(array $query = [])
    {
        return $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.show', ['pesertaMagang' => $this->peserta] + $query));
    }

    public function test_membaca_absensi_dan_melewati_baris_kosong(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->once()->with(self::URL)->andReturn(['SEPTEMBER 2026', 'Oktober 2026']);
            $mock->shouldReceive('getValuesFromUrl')->once()->with(self::URL, "'Oktober 2026'!B11:K43")->andReturn($this->isiSheet());
        });

        $response = $this->bukaDetail();

        $response->assertOk()
            ->assertSee('Membuat laporan')
            ->assertSee('value="16:00"', false)
            ->assertSee('name="sheet_row"', false)
            ->assertSee('value="15"', false)
            ->assertSee('Oktober 2026');

        $rekap = $response->viewData('rekap');
        $this->assertSame(4, $rekap['tercatat']);
        $this->assertSame(2, $rekap['hadir']);
        $this->assertSame(1, $rekap['izin']);
        $this->assertSame(1, $rekap['sakit']);

        // Baris yang hanya berisi nomor & placeholder dilewati;
        // nomor baris asli tetap benar.
        $this->assertSame([13, 15, 16, 17], array_column($response->viewData('sheetRows'), 'sheet_row'));
        $this->assertSame('', $response->viewData('sheetRows')[1]['action']);
    }

    public function test_tab_berbahasa_inggris_juga_dikenali(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andReturn(['OCTOBER 2026']);
            $mock->shouldReceive('getValuesFromUrl')->with(self::URL, "'OCTOBER 2026'!B11:K43")->andReturn($this->isiSheet());
        });

        $this->bukaDetail()->assertOk()->assertSee('Membuat laporan');
    }

    public function test_pilih_bulan_lain_membaca_tab_bulan_tersebut(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andReturn(['SEPTEMBER 2026', 'OKTOBER 2026']);
            $mock->shouldReceive('getValuesFromUrl')->once()->with(self::URL, "'SEPTEMBER 2026'!B11:K43")->andReturn([]);
        });

        $response = $this->bukaDetail(['bulan' => '2026-09']);

        $response->assertOk()->assertSee('<option value="2026-09" selected', false);
        $this->assertSame(
            ['2026-09', '2026-10', '2026-11', '2026-12'],
            array_keys($response->viewData('pilihanBulan'))
        );
    }

    public function test_tab_bulan_tidak_ada_menampilkan_tab_yang_tersedia(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andReturn(['JULI 2026', 'AGUSTUS 2026']);
            $mock->shouldNotReceive('getValuesFromUrl');
        });

        $this->bukaDetail()
            ->assertOk()
            ->assertSee('Tab bulan Oktober 2026 belum ada di Google Sheets peserta.')
            ->assertSee('JULI 2026, AGUSTUS 2026');
    }

    public function test_credential_tidak_ada_menampilkan_pesan_jelas(): void
    {
        config(['absensi.credentials_path' => storage_path('app/google/tidak-ada.json')]);

        $this->bukaDetail()
            ->assertOk()
            ->assertSee('file credential service account belum dipasang');
    }

    public function test_hasil_baca_disimpan_di_cache_dan_bisa_dimuat_ulang(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->twice()->andReturn(['OKTOBER 2026']);
            $mock->shouldReceive('getValuesFromUrl')->twice()->andReturn($this->isiSheet());
        });

        $this->bukaDetail()->assertOk();
        $this->bukaDetail()->assertOk(); // dari cache, tanpa memanggil Google

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.absensi.refresh', $this->peserta), ['bulan' => '2026-10'])
            ->assertRedirect(route('superadmin.peserta-magang.show', ['pesertaMagang' => $this->peserta, 'bulan' => '2026-10']));

        $this->bukaDetail()->assertOk(); // dibaca ulang dari Google
    }

    public function test_update_absensi_menulis_ke_baris_yang_benar(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andReturn(['OKTOBER 2026']);
            $mock->shouldReceive('updateValuesFromUrl')
                ->once()
                ->with(self::URL, "'OKTOBER 2026'!D15:G15", [['08:00', '12:00', '', '']])
                ->andReturn(4);
        });

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.absensi.update', $this->peserta), [
                'bulan' => '2026-10',
                'sheet_row' => 15,
                'pagi_masuk' => '08:00',
                'pagi_keluar' => '12:00',
            ])
            ->assertRedirect(route('superadmin.peserta-magang.show', ['pesertaMagang' => $this->peserta, 'bulan' => '2026-10']))
            ->assertSessionHas('success');
    }

    public function test_update_absensi_memvalidasi_input(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('updateValuesFromUrl');
        });

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.absensi.update', $this->peserta), [
                'bulan' => 'oktober',
                'sheet_row' => 5,
                'pagi_masuk' => '8 pagi',
            ])
            ->assertSessionHasErrors(['bulan', 'sheet_row', 'pagi_masuk']);
    }

    public function test_update_absensi_gagal_menampilkan_pesan_error(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andReturn(['OKTOBER 2026']);
            $mock->shouldReceive('updateValuesFromUrl')->andThrow(new \RuntimeException('403 Forbidden'));
        });

        $this->actingAs($this->admin)
            ->put(route('superadmin.peserta-magang.absensi.update', $this->peserta), [
                'bulan' => '2026-10',
                'sheet_row' => 13,
            ])
            ->assertSessionHas('error', 'Data absensi gagal diperbarui. Periksa koneksi dan izin Google Sheets.');
    }
}
