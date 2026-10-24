<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Membaca & menulis absensi peserta di Google Sheets.
 *
 * Setiap spreadsheet peserta punya satu tab per bulan, misalnya
 * "OKTOBER 2026" atau "OCTOBER 2026". Nama tab dicocokkan otomatis
 * (bahasa Indonesia atau Inggris, tanpa membedakan huruf besar/kecil).
 */
class AbsensiSheetService
{
    private const NAMA_BULAN_ID = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    public function __construct(
        private readonly GoogleSheetsService $sheets,
    ) {}

    /**
     * Kandidat nama tab untuk satu bulan.
     *
     * @return array<int, string>
     */
    public function kandidatNamaTab(CarbonInterface $bulan): array
    {
        return array_values(array_unique([
            Str::upper(self::NAMA_BULAN_ID[$bulan->month].' '.$bulan->year),
            // copy(): locale() mengubah instance asli, termasuk CarbonImmutable.
            Str::upper($bulan->copy()->locale('en')->format('F Y')),
        ]));
    }

    /**
     * Cari nama tab yang sebenarnya di spreadsheet untuk bulan tertentu.
     *
     * @throws TabBulanTidakDitemukanException
     */
    public function cariTab(string $url, CarbonInterface $bulan): string
    {
        $tersedia = Cache::remember(
            $this->cacheKey('tab', $url),
            config('absensi.cache_seconds'),
            fn () => $this->sheets->getSheetTitlesFromUrl($url)
        );

        $kandidat = $this->kandidatNamaTab($bulan);
        $normal = fn (string $judul) => Str::upper(preg_replace('/\s+/', ' ', trim($judul)));

        foreach ($tersedia as $judul) {
            if (in_array($normal($judul), $kandidat, true)) {
                return $judul;
            }
        }

        throw new TabBulanTidakDitemukanException($kandidat, $tersedia);
    }

    /**
     * Baris absensi satu bulan (dari cache bila masih berlaku).
     *
     * @return array{tab: string, rows: array<int, array<string, mixed>>, diambil_pada: string}
     */
    public function ambil(string $url, CarbonInterface $bulan): array
    {
        $tab = $this->cariTab($url, $bulan);

        return Cache::remember(
            $this->cacheKey('baris', $url, $bulan->format('Y-m')),
            config('absensi.cache_seconds'),
            fn () => [
                'tab' => $tab,
                'rows' => $this->mapBaris($this->sheets->getValuesFromUrl($url, $this->rangeBaca($tab))),
                'diambil_pada' => now()->toIso8601String(),
            ]
        );
    }

    /**
     * Baris absensi dari cache saja (tanpa memanggil Google), null bila belum ada.
     *
     * @return array{tab: string, rows: array<int, array<string, mixed>>, diambil_pada: string}|null
     */
    public function dariCache(string $url, CarbonInterface $bulan): ?array
    {
        return Cache::get($this->cacheKey('baris', $url, $bulan->format('Y-m')));
    }

    /**
     * Ubah waktu absensi (pagi/siang masuk-keluar) pada satu baris sheet.
     *
     * @param  array<string, string|null>  $waktu
     */
    public function perbaruiWaktu(string $url, CarbonInterface $bulan, int $sheetRow, array $waktu): void
    {
        $tab = $this->cariTab($url, $bulan);
        $kolom = config('absensi.editable_columns');

        $range = sprintf(
            "'%s'!%s%d:%s%d",
            $tab,
            $this->hurufKolom($kolom[0]),
            $sheetRow,
            $this->hurufKolom(end($kolom)),
            $sheetRow
        );

        $this->sheets->updateValuesFromUrl($url, $range, [
            array_map(fn (string $nama) => $waktu[$nama] ?? '', $kolom),
        ]);

        Cache::forget($this->cacheKey('baris', $url, $bulan->format('Y-m')));
    }

    /**
     * Setel kolom Action (verifikasi logbook) untuk beberapa baris sekaligus.
     *
     * @param  array<int, int>  $sheetRows
     */
    public function perbaruiAction(string $url, CarbonInterface $bulan, array $sheetRows, string $action): void
    {
        $tab = $this->cariTab($url, $bulan);
        $kolom = $this->hurufKolom('action');

        $data = [];

        foreach (array_unique($sheetRows) as $sheetRow) {
            $data[sprintf("'%s'!%s%d", $tab, $kolom, $sheetRow)] = [[$action]];
        }

        $this->sheets->batchUpdateValuesFromUrl($url, $data);

        Cache::forget($this->cacheKey('baris', $url, $bulan->format('Y-m')));
    }

    /**
     * Hapus cache spreadsheet peserta (daftar tab & data bulan tersebut).
     */
    public function lupakan(string $url, CarbonInterface $bulan): void
    {
        Cache::forget($this->cacheKey('tab', $url));
        Cache::forget($this->cacheKey('baris', $url, $bulan->format('Y-m')));
    }

    /**
     * Ubah data mentah Google Sheets menjadi baris absensi.
     *
     * @param  array<int, array<int, mixed>>  $values
     * @return array<int, array<string, mixed>>
     */
    public function mapBaris(array $values): array
    {
        $kolom = config('absensi.columns');
        $jumlahKolom = count($kolom);
        $headerRow = config('absensi.header_row');
        $barisHeader = config('absensi.first_data_row') - $headerRow;

        $rows = [];

        foreach ($values as $index => $row) {
            // Lewati baris header & sub header.
            if ($index < $barisHeader) {
                continue;
            }

            $row = array_combine(
                $kolom,
                array_slice(array_pad($row, $jumlahKolom, ''), 0, $jumlahKolom)
            );

            // Placeholder dropdown Action ("......") dianggap kosong.
            if (isset($row['action']) && preg_match('/^\s*[.\-–—]*\s*$/u', (string) $row['action'])) {
                $row['action'] = '';
            }

            // Lewati baris kosong. Kolom No diabaikan karena template
            // sudah menomori 1–31 walau tanggalnya belum diisi.
            $isi = array_diff_key($row, array_flip(['no', 'action']));

            if (collect($isi)->every(fn ($nilai) => trim((string) $nilai) === '')) {
                continue;
            }

            $rows[] = ['sheet_row' => $index + $headerRow] + $row;
        }

        return $rows;
    }

    /**
     * Range baca, contoh: 'OKTOBER 2026'!B11:K43
     */
    private function rangeBaca(string $tab): string
    {
        $kolom = config('absensi.columns');

        return sprintf(
            "'%s'!%s%d:%s%d",
            $tab,
            config('absensi.first_column'),
            config('absensi.header_row'),
            $this->hurufKolom(end($kolom)),
            config('absensi.last_row')
        );
    }

    /**
     * Huruf kolom sheet untuk nama kolom, berdasarkan first_column.
     */
    private function hurufKolom(string $nama): string
    {
        $posisi = array_search($nama, config('absensi.columns'), true);
        $awal = ord(Str::upper(config('absensi.first_column'))) - ord('A');

        return chr(ord('A') + $awal + $posisi);
    }

    private function cacheKey(string $jenis, string $url, ?string $bulan = null): string
    {
        return 'absensi:'.$jenis.':'.md5($url.'|'.$bulan);
    }
}
