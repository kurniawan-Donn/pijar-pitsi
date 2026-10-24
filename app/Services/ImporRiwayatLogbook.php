<?php

namespace App\Services;

use App\Models\LogbookHarian;
use App\Models\PesertaMagang;
use Illuminate\Support\Facades\DB;
use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Reader\XLSX\Reader;

/**
 * Impor riwayat logbook dari buku kerja lama (Google Sheets peserta atau
 * file .xlsx) ke tabel logbook_harians dengan sumber "impor".
 *
 * Tanggal yang sudah ada di database tidak ditimpa, dan hanya tanggal
 * dalam masa magang yang disimpan. Tab yang bukan sheet bulanan
 * (profil, penilaian, dll.) dilewati otomatis.
 */
class ImporRiwayatLogbook
{
    /**
     * Baris & kolom yang dibaca per tab (sheet bulanan berisi ±60 baris).
     */
    private const MAKS_BARIS = 80;

    private const RANGE_SHEETS = 'A1:T80';

    public function __construct(
        private GoogleSheetsService $sheets,
        private ParserSheetLogbook $parser,
        private KalenderLogbook $kalender,
    ) {}

    /**
     * @return array<string, mixed> hasil impor (lihat simpan())
     */
    public function dariGoogleSheets(PesertaMagang $peserta): array
    {
        $tabs = [];

        foreach ($this->sheets->getSheetTitlesFromUrl($peserta->google_sheet_url) as $judul) {
            $tabs[$judul] = $this->sheets->getValuesFromUrl(
                $peserta->google_sheet_url,
                "'".str_replace("'", "''", $judul)."'!".self::RANGE_SHEETS,
                ['valueRenderOption' => 'UNFORMATTED_VALUE', 'dateTimeRenderOption' => 'SERIAL_NUMBER']
            );
        }

        return $this->simpan($peserta, $tabs);
    }

    /**
     * @return array<string, mixed> hasil impor (lihat simpan())
     */
    public function dariXlsx(PesertaMagang $peserta, string $path): array
    {
        $reader = new Reader(new Options(SHOULD_FORMAT_DATES: false, SHOULD_PRESERVE_EMPTY_ROWS: true));
        $reader->open($path);
        $tabs = [];

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $rows = [];

                foreach ($sheet->getRowIterator() as $row) {
                    $rows[] = $row->toArray();

                    if (count($rows) >= self::MAKS_BARIS) {
                        break;
                    }
                }

                $tabs[$sheet->getName()] = $rows;
            }
        } finally {
            $reader->close();
        }

        return $this->simpan($peserta, $tabs);
    }

    /**
     * Parse setiap tab lalu simpan baris yang belum ada.
     *
     * @param  array<string, array<int, array<int, mixed>>>  $tabs  [nama tab => baris]
     * @return array{tab: array<int, array{nama: string, format: string, baris: int, dibuat: int}>, dilewati: array<int, string>, dibuat: int, sudah_ada: int, di_luar: int, peringatan: array<int, string>}
     */
    public function simpan(PesertaMagang $peserta, array $tabs): array
    {
        $hasil = ['tab' => [], 'dilewati' => [], 'dibuat' => 0, 'sudah_ada' => 0, 'di_luar' => 0, 'peringatan' => []];

        $sudahAda = LogbookHarian::where('peserta_magang_id', $peserta->id)
            ->get(['tanggal'])
            ->map(fn (LogbookHarian $logbook) => $logbook->tanggal->toDateString())
            ->flip()
            ->all();

        DB::transaction(function () use ($peserta, $tabs, &$hasil, &$sudahAda) {
            foreach ($tabs as $nama => $rows) {
                $parse = $this->parser->parse($rows);

                if ($parse['format'] === null) {
                    $hasil['dilewati'][] = (string) $nama;

                    continue;
                }

                $dibuat = 0;

                foreach ($parse['baris'] as $baris) {
                    if (! $this->kalender->dalamMasaMagang($peserta, new \DateTimeImmutable($baris['tanggal']))) {
                        $hasil['di_luar']++;

                        continue;
                    }

                    if (isset($sudahAda[$baris['tanggal']])) {
                        $hasil['sudah_ada']++;

                        continue;
                    }

                    LogbookHarian::create([
                        ...$baris,
                        'peserta_magang_id' => $peserta->id,
                        'lokasi' => $baris['lokasi'] ? mb_substr($baris['lokasi'], 0, 255) : null,
                        'sumber' => 'impor',
                    ]);

                    $sudahAda[$baris['tanggal']] = true;
                    $dibuat++;
                }

                $hasil['tab'][] = ['nama' => (string) $nama, 'format' => $parse['format'], 'baris' => count($parse['baris']), 'dibuat' => $dibuat];
                $hasil['dibuat'] += $dibuat;

                foreach ($parse['peringatan'] as $pesan) {
                    $hasil['peringatan'][] = "{$nama} · {$pesan}";
                }
            }
        });

        return $hasil;
    }

    /**
     * Hapus semua hari hasil impor yang belum punya bukti (untuk impor ulang).
     */
    public function hapus(PesertaMagang $peserta): int
    {
        return LogbookHarian::where('peserta_magang_id', $peserta->id)
            ->where('sumber', 'impor')
            ->whereDoesntHave('buktis')
            ->delete();
    }
}
