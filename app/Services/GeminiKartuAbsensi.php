<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Panggilan ke Gemini (REST generateContent) untuk membaca jam dari foto kartu absensi.
 * Keluaran dipaksa JSON lewat responseJsonSchema; kunci API dari config/services.php (.env).
 */
class GeminiKartuAbsensi
{
    private const URL = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';

    /**
     * @return array{baris: array<int, array<string, mixed>>, model: string, token_masuk: ?int, token_keluar: ?int}
     */
    public function baca(string $jpeg, string $prompt): array
    {
        $kunci = config('services.gemini.key');
        $model = config('services.gemini.model');

        if (blank($kunci)) {
            throw new GagalBacaKartuException('Fitur scan belum siap: kunci API Gemini belum diatur. Hubungi admin.');
        }

        try {
            $respons = Http::withHeaders(['x-goog-api-key' => $kunci])
                ->acceptJson()
                ->timeout(config('services.gemini.timeout'))
                // Ulang hanya untuk gangguan sementara, bukan kesalahan permintaan.
                ->retry(2, 1000, fn ($e) => $e instanceof ConnectionException
                    || ($e instanceof RequestException && ($e->response->serverError() || $e->response->status() === 429)), throw: false)
                ->post(sprintf(self::URL, $model), [
                    'contents' => [[
                        'parts' => [
                            ['text' => $prompt],
                            ['inline_data' => ['mime_type' => 'image/jpeg', 'data' => base64_encode($jpeg)]],
                        ],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0,
                        'responseMimeType' => 'application/json',
                        'responseJsonSchema' => $this->skema(),
                    ],
                ]);
        } catch (ConnectionException) {
            throw new GagalBacaKartuException('Tidak dapat terhubung ke layanan pembaca kartu. Coba lagi beberapa saat lagi.');
        }

        if (! $respons->successful()) {
            // Hanya status dan pesan singkat yang dicatat; tidak ada kunci atau gambar.
            Log::warning('Gemini kartu absensi gagal', ['status' => $respons->status(), 'pesan' => mb_substr((string) $respons->json('error.message'), 0, 200)]);

            throw new GagalBacaKartuException($respons->status() === 429
                ? 'Layanan pembaca kartu sedang sibuk. Coba lagi beberapa menit lagi.'
                : 'Layanan pembaca kartu sedang bermasalah. Coba lagi nanti atau isi jam secara manual.');
        }

        $teks = (string) $respons->json('candidates.0.content.parts.0.text');
        $data = json_decode($teks, true);

        if (! is_array($data) || ! isset($data['baris']) || ! is_array($data['baris'])) {
            throw new GagalBacaKartuException('Kartu tidak terbaca. Pastikan foto terang, tegak, dan seluruh tabel terlihat, lalu coba lagi.');
        }

        $pemakaian = $respons->json('usageMetadata', []);

        return [
            'baris' => array_values(array_filter($data['baris'], 'is_array')),
            'model' => (string) ($respons->json('modelVersion') ?: $model),
            'token_masuk' => $pemakaian['promptTokenCount'] ?? null,
            // Token "berpikir" ditagih sebagai keluaran.
            'token_keluar' => isset($pemakaian['candidatesTokenCount']) || isset($pemakaian['thoughtsTokenCount'])
                ? ($pemakaian['candidatesTokenCount'] ?? 0) + ($pemakaian['thoughtsTokenCount'] ?? 0)
                : null,
        ];
    }

    /**
     * Skema keluaran (JSON Schema standar): satu baris per tanggal yang punya cetakan jam.
     * Kolom jam dibatasi pola HH:MM agar model tidak menulis penjelasan di dalamnya.
     *
     * @return array<string, mixed>
     */
    private function skema(): array
    {
        $jam = [
            'anyOf' => [
                ['type' => 'string', 'pattern' => '^([01][0-9]|2[0-3]):[0-5][0-9]$'],
                ['type' => 'null'],
            ],
        ];

        return [
            'type' => 'object',
            'properties' => [
                'baris' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'tanggal' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 31, 'description' => 'Tanggal (1–31) tempat jam ini paling masuk akal.'],
                            'jam_masuk' => $jam + ['description' => 'Pagi Masuk, HH:MM 24 jam, atau null.'],
                            'jam_keluar' => $jam + ['description' => 'Siang Keluar, HH:MM 24 jam, atau null.'],
                            'keyakinan' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                            'catatan' => ['type' => ['string', 'null'], 'description' => 'Alasan singkat (maks. 15 kata) bila ragu.'],
                        ],
                        'required' => ['tanggal', 'jam_masuk', 'jam_keluar', 'keyakinan', 'catatan'],
                        'propertyOrdering' => ['tanggal', 'jam_masuk', 'jam_keluar', 'keyakinan', 'catatan'],
                    ],
                ],
            ],
            'required' => ['baris'],
        ];
    }
}
