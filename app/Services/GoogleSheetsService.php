<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use RuntimeException;

class GoogleSheetsService
{
    protected Client $client;

    protected Sheets $service;

    public function __construct()
    {
        $credentialsPath = storage_path(
            'app/google/service-account.json'
        );

        if (!file_exists($credentialsPath)) {
            throw new RuntimeException(
                'File credential Google Sheets tidak ditemukan: '
                . $credentialsPath
            );
        }

        $this->client = new Client();

        $this->client->setApplicationName(
            config('app.name', 'MAGENTA')
        );

        $this->client->setAuthConfig($credentialsPath);

        $this->client->setScopes([
            Sheets::SPREADSHEETS,
        ]);

        $this->service = new Sheets($this->client);
    }

    /**
     * Ambil Spreadsheet ID dari URL Google Sheets.
     */
    public function extractSpreadsheetId(string $url): string
    {
        $pattern = '/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/';

        if (!preg_match($pattern, $url, $matches)) {
            throw new RuntimeException(
                'URL Google Sheets tidak valid.'
            );
        }

        return $matches[1];
    }

    /**
     * Membaca data dari Google Sheets.
     */
    public function getValues(
        string $spreadsheetId,
        string $range = 'A1:Z100'
    ): array {
        $response = $this->service
            ->spreadsheets_values
            ->get(
                $spreadsheetId,
                $range
            );

        return $response->getValues() ?? [];
    }

    /**
     * Membaca data langsung menggunakan URL Google Sheets.
     */
    public function getValuesFromUrl(
        string $url,
        string $range = 'A1:Z100'
    ): array {
        $spreadsheetId = $this->extractSpreadsheetId($url);

        return $this->getValues(
            $spreadsheetId,
            $range
        );
    }

    /**
     * Mendapatkan daftar nama tab/sheet
     * di dalam satu spreadsheet.
     */
    public function getSheetTitles(
        string $spreadsheetId
    ): array {
        $spreadsheet = $this->service
            ->spreadsheets
            ->get($spreadsheetId);

        $sheets = $spreadsheet->getSheets();

        return collect($sheets)
            ->map(function ($sheet) {
                return $sheet
                    ->getProperties()
                    ->getTitle();
            })
            ->values()
            ->all();
    }

    /**
     * Mengubah / menulis data ke Google Sheets.
     */
    public function updateValues(
        string $spreadsheetId,
        string $range,
        array $values
    ): int {
        $body = new ValueRange([
            'values' => $values,
        ]);

        $params = [
            'valueInputOption' => 'USER_ENTERED',
        ];

        $response = $this->service
            ->spreadsheets_values
            ->update(
                $spreadsheetId,
                $range,
                $body,
                $params
            );

        return (int) $response->getUpdatedCells();
    }

    /**
     * Mengubah / menulis data menggunakan URL Google Sheets.
     */
    public function updateValuesFromUrl(
        string $url,
        string $range,
        array $values
    ): int {
        $spreadsheetId = $this->extractSpreadsheetId($url);

        return $this->updateValues(
            $spreadsheetId,
            $range,
            $values
        );
    }
}