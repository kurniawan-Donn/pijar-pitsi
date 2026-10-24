<?php

namespace Tests\Feature;

use App\Models\AspekPenilaian;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use App\Models\User;
use App\Services\GoogleSheetsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 09:00:00');

        $this->admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Uji']);
    }

    private function peserta(array $data): PesertaMagang
    {
        return PesertaMagang::create(array_merge(['status' => 'aktif'], $data));
    }

    public function test_tamu_tidak_bisa_membuka_laporan(): void
    {
        $this->get(route('superadmin.laporan.index'))->assertRedirect(route('login'));
        $this->get(route('superadmin.laporan.csv'))->assertRedirect(route('login'));
        $this->get(route('superadmin.laporan.cetak'))->assertRedirect(route('login'));
    }

    public function test_rekap_peserta_difilter_berdasarkan_irisan_periode(): void
    {
        $this->peserta(['nama' => 'Periode Agustus', 'periode_mulai' => '2026-08-01', 'periode_selesai' => '2026-08-31']);
        $this->peserta(['nama' => 'Periode Sep-Nov', 'periode_mulai' => '2026-09-15', 'periode_selesai' => '2026-11-15']);
        $this->peserta(['nama' => 'Periode Desember', 'periode_mulai' => '2026-12-01', 'periode_selesai' => '2026-12-31']);
        // Batas tepat di tanggal akhir & awal filter tetap ikut.
        $this->peserta(['nama' => 'Mulai Akhir Oktober', 'periode_mulai' => '2026-10-31', 'periode_selesai' => '2026-12-31']);
        $this->peserta(['nama' => 'Selesai Awal Oktober', 'periode_mulai' => '2026-09-01', 'periode_selesai' => '2026-10-01']);

        $response = $this->actingAs($this->admin)->get(route('superadmin.laporan.index', [
            'jenis' => 'peserta',
            'dari' => '2026-10-01',
            'sampai' => '2026-10-31',
        ]));

        $response->assertOk()
            ->assertSee('Periode Sep-Nov')
            ->assertSee('Mulai Akhir Oktober')
            ->assertSee('Selesai Awal Oktober')
            ->assertDontSee('Periode Agustus')
            ->assertDontSee('Periode Desember');

        $this->assertSame(3, $response->viewData('laporan')['ringkasan']['Jumlah peserta']);
    }

    public function test_filter_tanggal_tidak_valid_ditolak(): void
    {
        $this->actingAs($this->admin)
            ->get(route('superadmin.laporan.index', ['dari' => '2026-10-31', 'sampai' => '2026-10-01']))
            ->assertSessionHasErrors('sampai');
    }

    public function test_export_csv_berisi_bom_pemisah_titik_koma_dan_data(): void
    {
        $this->peserta(['nama' => 'Rina Pratiwi', 'nim' => '2201', 'asal_kampus' => 'Universitas Contoh']);

        $response = $this->actingAs($this->admin)->get(route('superadmin.laporan.csv', ['jenis' => 'peserta']));

        $response->assertOk()->assertDownload();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('attachment; filename=laporan-peserta-', $response->headers->get('Content-Disposition'));

        $isi = $response->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $isi);
        $this->assertStringContainsString('No;Nama;NIM;"Asal Kampus"', $isi);
        $this->assertStringContainsString('1;"Rina Pratiwi";2201;"Universitas Contoh"', $isi);
    }

    public function test_export_xlsx_berisi_judul_kolom_dan_data(): void
    {
        $this->peserta(['nama' => 'Rina Pratiwi', 'nim' => '2201', 'asal_kampus' => 'Universitas Contoh']);

        $response = $this->actingAs($this->admin)->get(route('superadmin.laporan.xlsx', ['jenis' => 'peserta']));

        $response->assertOk()->assertDownload();
        $this->assertStringEndsWith('.xlsx', $response->headers->get('Content-Disposition'));

        $reader = new XlsxReader;
        $reader->open($response->getFile()->getPathname());

        $baris = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            $this->assertSame('Rekap Peserta', $sheet->getName());

            foreach ($sheet->getRowIterator() as $row) {
                $baris[] = $row->toArray();
            }
        }
        $reader->close();

        // Reader melewati baris kosong, jadi cari baris judul kolom.
        $indeksKolom = collect($baris)->search(fn (array $row) => ($row[0] ?? null) === 'No');

        $this->assertSame('Rekap Peserta Magang', $baris[0][0]);
        $this->assertNotFalse($indeksKolom);
        $this->assertSame(['No', 'Nama', 'NIM', 'Asal Kampus'], array_slice($baris[$indeksKolom], 0, 4));
        $this->assertSame([1, 'Rina Pratiwi', '2201', 'Universitas Contoh'], array_slice($baris[$indeksKolom + 1], 0, 4));
        // Reader mengisi sel kosong di kanan; cukup cek dua sel pertama.
        $this->assertContains(['Jumlah peserta', 1], array_map(fn (array $row) => array_slice($row, 0, 2), $baris));
    }

    public function test_export_pdf_menghasilkan_file_pdf(): void
    {
        $this->peserta(['nama' => 'Peserta PDF']);

        $response = $this->actingAs($this->admin)->get(route('superadmin.laporan.pdf', ['jenis' => 'peserta']));

        $response->assertOk()->assertDownload();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_tamu_tidak_bisa_mengunduh_xlsx_dan_pdf(): void
    {
        $this->get(route('superadmin.laporan.xlsx'))->assertRedirect(route('login'));
        $this->get(route('superadmin.laporan.pdf'))->assertRedirect(route('login'));
    }

    public function test_versi_cetak_menampilkan_laporan_dan_tombol_cetak(): void
    {
        $this->peserta(['nama' => 'Peserta Cetak']);

        $this->actingAs($this->admin)
            ->get(route('superadmin.laporan.cetak', ['jenis' => 'peserta']))
            ->assertOk()
            ->assertSee('Rekap Peserta Magang')
            ->assertSee('Peserta Cetak')
            ->assertSee('window.print()', false)
            ->assertSee('Admin Uji');
    }

    public function test_rekap_absensi_menghitung_uang_saku_dan_menandai_sheet_bermasalah(): void
    {
        $this->peserta(['nama' => 'Ada Sheet', 'google_sheet_url' => 'https://docs.google.com/spreadsheets/d/ada/edit']);
        $this->peserta(['nama' => 'Tanpa Sheet']);
        $this->peserta(['nama' => 'Tab Hilang', 'google_sheet_url' => 'https://docs.google.com/spreadsheets/d/hilang/edit']);

        $this->mock(GoogleSheetsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSheetTitlesFromUrl')
                ->with('https://docs.google.com/spreadsheets/d/ada/edit')->andReturn(['OKTOBER 2026']);
            $mock->shouldReceive('getSheetTitlesFromUrl')
                ->with('https://docs.google.com/spreadsheets/d/hilang/edit')->andReturn(['SEPTEMBER 2026']);
            $mock->shouldReceive('getValuesFromUrl')->andReturn([
                ['No'], [''],
                ['1', '01/10', '08:00', '12:00', '13:00', '16:00', 'Coding', 'Fitur', 'Kantor', 'Diterima'],
                ['2', '02/10', '08:00', '12:00', '', '', 'Rapat', '', '', '......'],
                ['3', '03/10', '', '', '', '', 'Sakit', '', '', ''],
            ]);
        });

        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.laporan.index', ['jenis' => 'absensi', 'bulan' => '2026-10']));

        $response->assertOk()
            ->assertSee('Belum ada link Google Sheets')
            ->assertSee('Tab bulan tidak ada');

        $baris = collect($response->viewData('laporan')['baris'])->keyBy(1);

        // Hari Tercatat, Hadir, Penuh, Setengah, Izin, Sakit, Logbook, Uang saku
        $this->assertSame([3, 2, 1, 1, 0, 1, '1/3', 75000], array_slice($baris['Ada Sheet'], 3, 8));
        $this->assertSame('Rp 75.000', $response->viewData('laporan')['ringkasan']['Total uang saku']);
        $this->assertSame(2, $response->viewData('laporan')['ringkasan']['Data tidak terbaca']);
    }

    public function test_laporan_nilai_memuat_kolom_aspek_dan_rata_rata(): void
    {
        $peserta = $this->peserta(['nama' => 'Peserta Bernilai']);
        $aspek = AspekPenilaian::aktif()->get();

        $penilaian = PenilaianPeserta::create([
            'peserta_magang_id' => $peserta->id,
            'nilai_akhir' => 86.5,
            'predikat' => 'A',
            'dinilai_oleh' => $this->admin->id,
            'dinilai_pada' => now(),
        ]);

        foreach ($aspek as $item) {
            $penilaian->details()->create([
                'aspek_penilaian_id' => $item->id,
                'nama_aspek' => $item->nama,
                'bobot' => $item->bobot,
                'nilai' => 86,
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('superadmin.laporan.index', ['jenis' => 'nilai']));

        $laporan = $response->viewData('laporan');

        $response->assertOk()->assertSee('Peserta Bernilai')->assertSee('86,50');
        $this->assertContains('Kemampuan Teknis', $laporan['kolom']);
        $this->assertSame('86,50', $laporan['ringkasan']['Rata-rata']);
    }
}
