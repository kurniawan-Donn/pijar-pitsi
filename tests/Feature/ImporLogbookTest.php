<?php

namespace Tests\Feature;

use App\Models\LogbookHarian;
use App\Models\PesertaMagang;
use App\Models\User;
use App\Services\GoogleSheetsService;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mockery\MockInterface;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Tests\TestCase;

class ImporLogbookTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private PesertaMagang $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->peserta = PesertaMagang::create([
            'nama' => 'Dony',
            'status' => 'aktif',
            'periode_mulai' => '2026-06-03',
            'periode_selesai' => '2026-11-30',
            'google_sheet_url' => 'https://docs.google.com/spreadsheets/d/abc123/edit',
        ]);
    }

    /**
     * Tab SEPTEMBER format baru dalam bentuk nilai mentah Google Sheets.
     *
     * @return array<int, array<int, mixed>>
     */
    private function tabSeptember(): array
    {
        return [
            ['', 'DAILY AKTIVITY MAHASISWA MAGANG'],
            ['', 'No', 'Tanggal', 'Pagi', '', 'Siang', '', 'Activity', 'Output', 'Lokasi', 'Action'],
            ['', '', '', 'Masuk', 'Keluar', 'Masuk', 'Keluar'],
            ['', 1, 46262, '08.00', '', '', '16.30', 'Finalisasi mockup', '', 'Kantor Jogja', 'Diterima'],
            ['', 2, 46265, '08.00', '', '', '16.30', 'Prototype chatbot', '', 'Kantor Jogja', 'Review'],
            ['', 3, 46269, '', '', '', '', 'Izin istirahat', '', '', ' .......'],
        ];
    }

    private function mockSheets(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andReturn(['PROFIL MAHASISWA', 'SEPTEMBER 2026']);
            $mock->shouldReceive('getValuesFromUrl')
                ->withArgs(fn ($url, $range, $opsi) => str_starts_with($range, "'PROFIL MAHASISWA'!") && $opsi['valueRenderOption'] === 'UNFORMATTED_VALUE')
                ->andReturn([['', 'PROFILE MAHASISWA MAGANG']]);
            $mock->shouldReceive('getValuesFromUrl')
                ->withArgs(fn ($url, $range) => str_starts_with($range, "'SEPTEMBER 2026'!"))
                ->andReturn($this->tabSeptember());
        });
    }

    public function test_impor_dari_google_sheets(): void
    {
        $this->mockSheets();

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.impor-logbook.sheets', $this->peserta))
            ->assertRedirect(route('superadmin.peserta-magang.logbook-harian', $this->peserta).'#impor')
            ->assertSessionHas('success', 'Impor dari Google Sheets selesai: 3 hari ditambahkan.')
            ->assertSessionHas('hasil_impor', fn (array $hasil) => $hasil['dilewati'] === ['PROFIL MAHASISWA'] && $hasil['tab'][0]['dibuat'] === 3);

        $hari = LogbookHarian::where('peserta_magang_id', $this->peserta->id)->orderBy('tanggal')->get();

        $this->assertCount(3, $hari);
        $this->assertSame(['impor'], $hari->pluck('sumber')->unique()->values()->all());
        $this->assertSame('2026-08-28', $hari[0]->tanggal->toDateString());
        $this->assertSame('diterima', $hari[0]->status_verifikasi);
        $this->assertSame('hari_penuh', $hari[0]->jenisKehadiran());
        $this->assertSame('review', $hari[1]->status_verifikasi);
        $this->assertSame('izin', $hari[2]->kehadiran);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'impor_logbook', 'subjek_id' => $this->peserta->id]);

        // Laporan hasil tampil di halaman logbook.
        $this->actingAs($this->admin)
            ->get(route('superadmin.peserta-magang.logbook-harian', [$this->peserta, 'periode' => '2026-09']))
            ->assertSee('Finalisasi mockup')
            ->assertSee('Hapus Hasil Impor (3 hari)');
    }

    public function test_impor_tidak_menimpa_data_website_dan_hanya_masa_magang(): void
    {
        $this->peserta->update(['periode_mulai' => '2026-08-31']);

        LogbookHarian::create([
            'peserta_magang_id' => $this->peserta->id,
            'tanggal' => '2026-08-31',
            'kehadiran' => 'hadir',
            'activity' => 'Diisi di website',
        ]);

        $this->mockSheets();

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.impor-logbook.sheets', $this->peserta))
            ->assertSessionHas('hasil_impor', fn (array $hasil) => $hasil['dibuat'] === 1 && $hasil['sudah_ada'] === 1 && $hasil['di_luar'] === 1);

        $this->assertSame('Diisi di website', LogbookHarian::whereDate('tanggal', '2026-08-31')->value('activity'));
    }

    public function test_impor_ulang_tidak_menggandakan(): void
    {
        $this->mockSheets();

        $this->actingAs($this->admin)->post(route('superadmin.peserta-magang.impor-logbook.sheets', $this->peserta));
        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.impor-logbook.sheets', $this->peserta))
            ->assertSessionHas('warning', 'Impor dari Google Sheets selesai: 0 hari ditambahkan.');

        $this->assertSame(3, LogbookHarian::count());
    }

    public function test_sheet_tidak_terbaca_memberi_pesan(): void
    {
        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')->andThrow(new \RuntimeException('403'));
        });

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.impor-logbook.sheets', $this->peserta))
            ->assertSessionHas('error', 'Google Sheets peserta tidak dapat dibaca. Pastikan sheet dibagikan ke service account aplikasi.');
    }

    public function test_impor_dari_file_xlsx(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'buku').'.xlsx';
        $writer = new Writer;
        $writer->openToFile($path);
        $writer->getCurrentSheet()->setName('PROFIL MAHASISWA');
        $writer->addRow(Row::fromValues(['', 'PROFILE MAHASISWA MAGANG']));
        $writer->addNewSheetAndMakeItCurrent()->setName('JULI 2026');

        foreach ([
            ['', 'No', 'Tanggal', 'Pagi', '', 'Siang', '', 'Keterangan', 'Paraf'],
            ['', '', '', 'Masuk', 'Keluar', 'Masuk', 'Keluar'],
            ['', 1, new DateTimeImmutable('2026-06-28'), '', '', '', '', 'Minggu', ''],
            ['', 2, new DateTimeImmutable('2026-06-29'), '08.08', '', '', '16.44', 'Koordinasi akhir divisi', 'v'],
            ['', 3, new DateTimeImmutable('2026-07-10'), '', '', '', '', 'Tidak hadir dikarenakan sakit', ''],
            ['', 'Total Masuk Full', '', ': 1 hari'],
        ] as $baris) {
            $writer->addRow(Row::fromValues($baris));
        }

        $writer->close();

        $berkas = new UploadedFile($path, 'absensi-dony.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.impor-logbook.xlsx', $this->peserta), ['berkas' => $berkas])
            ->assertSessionHas('success', 'Impor dari file absensi-dony.xlsx selesai: 3 hari ditambahkan.');

        $hari = LogbookHarian::orderBy('tanggal')->get()->keyBy(fn ($item) => $item->tanggal->toDateString());

        $this->assertSame('libur', $hari['2026-06-28']->kehadiran);
        $this->assertSame('08:08', $hari['2026-06-29']->jam('pagi_masuk'));
        $this->assertSame('diterima', $hari['2026-06-29']->status_verifikasi);
        $this->assertSame('sakit', $hari['2026-07-10']->kehadiran);

        @unlink($path);
    }

    public function test_file_bukan_xlsx_ditolak(): void
    {
        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.impor-logbook.xlsx', $this->peserta), [
                'berkas' => UploadedFile::fake()->create('data.csv', 5, 'text/csv'),
            ])
            ->assertSessionHasErrorsIn('impor', 'berkas');
    }

    public function test_peserta_tanpa_periode_tidak_bisa_diimpor(): void
    {
        $this->peserta->update(['periode_mulai' => null]);

        $this->actingAs($this->admin)
            ->post(route('superadmin.peserta-magang.impor-logbook.sheets', $this->peserta))
            ->assertSessionHas('error', 'Isi tanggal mulai dan selesai magang peserta sebelum mengimpor.');
    }

    public function test_hapus_hasil_impor_menyisakan_isian_website(): void
    {
        $this->mockSheets();
        $this->actingAs($this->admin)->post(route('superadmin.peserta-magang.impor-logbook.sheets', $this->peserta));

        LogbookHarian::create([
            'peserta_magang_id' => $this->peserta->id,
            'tanggal' => '2026-10-01',
            'kehadiran' => 'hadir',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('superadmin.peserta-magang.impor-logbook.hapus', $this->peserta))
            ->assertSessionHas('success', '3 hari hasil impor dihapus.');

        $this->assertSame(1, LogbookHarian::count());
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'hapus_impor_logbook']);
    }
}
