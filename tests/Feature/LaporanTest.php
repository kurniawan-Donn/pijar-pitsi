<?php

namespace Tests\Feature;

use App\Models\AspekPenilaian;
use App\Models\LogbookHarian;
use App\Models\PenilaianPeserta;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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

    public function test_rekap_kehadiran_dan_uang_saku_per_periode_dari_logbook(): void
    {
        // Hari ini 15 Okt 2026 → periode Oktober = 28 Sep – 27 Okt 2026.
        $aktif = $this->peserta(['nama' => 'Peserta Rajin', 'periode_mulai' => '2026-09-01', 'periode_selesai' => '2026-12-31']);
        $this->peserta(['nama' => 'Belum Mulai', 'periode_mulai' => '2026-11-01', 'periode_selesai' => '2026-12-31']);

        foreach ([
            ['2026-09-28', ['pagi_masuk' => '08:00', 'siang_keluar' => '16:30']],                       // penuh
            ['2026-09-29', ['pagi_masuk' => '08:00', 'pagi_keluar' => '12:00']],                        // setengah
            ['2026-09-30', ['pagi_masuk' => '08:00', 'siang_keluar' => '16:30', 'status_verifikasi' => 'ditolak']],
            ['2026-10-01', ['kehadiran' => 'sakit']],
        ] as [$tanggal, $data]) {
            LogbookHarian::create($data + [
                'peserta_magang_id' => $aktif->id,
                'tanggal' => $tanggal,
                'kehadiran' => 'hadir',
                'activity' => 'Kegiatan',
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.laporan.index', ['jenis' => 'absensi', 'periode' => '2026-10']));

        $laporan = $response->viewData('laporan');

        $response->assertOk()->assertSee('Periode Oktober 2026 (28 Sep – 27 Okt 2026)');
        $this->assertSame(['Peserta Rajin'], array_column($laporan['baris'], 1));

        // Hari Penuh, Setengah, Izin, Sakit, Libur, Belum Diisi, Menunggu, Uang Saku
        $baris = $laporan['baris'][0];
        $this->assertSame([1, 1, 0, 1], array_slice($baris, 4, 4));
        $this->assertSame(75000, $baris[11]);
        $this->assertSame('Rp 75.000', $laporan['ringkasan']['Total uang saku']);
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
        $this->assertContains('Kolaborasi Tim', $laporan['kolom']);
        $this->assertSame('86,50', $laporan['ringkasan']['Rata-rata']);
    }
}
