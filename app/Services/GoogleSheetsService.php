<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use RuntimeException;

class GoogleSheetsService
{
    protected ?Sheets $service = null;

    /**
     * Client Google baru dibuat saat pertama kali dipakai, sehingga
     * service ini aman di-resolve (dan di-mock) walau credential belum ada.
     */
    public function __construct(
        protected ?string $credentialsPath = null,
    ) {
        $this->credentialsPath ??= config('absensi.credentials_path');
    }

    /**
     * Inisialisasi koneksi Google Sheets.
     */
    protected function sheets(): Sheets
    {
        if ($this->service) {
            return $this->service;
        }

        if (! $this->credentialsPath || ! file_exists($this->credentialsPath)) {
            throw new CredentialTidakDitemukanException(
                'File credential Google Sheets tidak ditemukan: '.$this->credentialsPath
            );
        }

        $client = new Client;
        $client->setApplicationName(config('app.name', 'MAGENTA'));
        $client->setAuthConfig($this->credentialsPath);
        $client->setScopes([Sheets::SPREADSHEETS]);

        return $this->service = new Sheets($client);
    }

    /**
     * Ambil Spreadsheet ID dari URL Google Sheets.
     */
    public function extractSpreadsheetId(string $url): string
    {
        if (! preg_match('/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            throw new RuntimeException('URL Google Sheets tidak valid.');
        }

        return $matches[1];
    }

    /**
     * Membaca data dari Google Sheets.
     */
    public function getValues(string $spreadsheetId, string $range = 'A1:Z100'): array
    {
        $response = $this->sheets()->spreadsheets_values->get($spreadsheetId, $range);

        return $response->getValues() ?? [];
    }

    /**
     * Membaca data langsung menggunakan URL Google Sheets.
     */
    public function getValuesFromUrl(string $url, string $range = 'A1:Z100'): array
    {
        return $this->getValues($this->extractSpreadsheetId($url), $range);
    }

    /**
     * Daftar nama tab/sheet di dalam satu spreadsheet.
     *
     * @return array<int, string>
     */
    public function getSheetTitles(string $spreadsheetId): array
    {
        $spreadsheet = $this->sheets()->spreadsheets->get($spreadsheetId);

        return collect($spreadsheet->getSheets())
            ->map(fn ($sheet) => $sheet->getProperties()->getTitle())
            ->values()
            ->all();
    }

    /**
     * Daftar nama tab menggunakan URL Google Sheets.
     *
     * @return array<int, string>
     */
    public function getSheetTitlesFromUrl(string $url): array
    {
        return $this->getSheetTitles($this->extractSpreadsheetId($url));
    }

    /**
     * Mengubah / menulis data ke Google Sheets.
     */
    public function updateValues(string $spreadsheetId, string $range, array $values): int
    {
        $response = $this->sheets()->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            new ValueRange(['values' => $values]),
            ['valueInputOption' => 'USER_ENTERED']
        );

        return (int) $response->getUpdatedCells();
    }

    /**
     * Mengubah / menulis data menggunakan URL Google Sheets.
     */
    public function updateValuesFromUrl(string $url, string $range, array $values): int
    {
        return $this->updateValues($this->extractSpreadsheetId($url), $range, $values);
    }
}
