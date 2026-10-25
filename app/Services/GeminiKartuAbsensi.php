<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Panggilan ke Gemini (REST generateContent) untuk membaca jam dari foto kartu absensi.
 * Keluaran dipaksa JSON lewat responseSchema; kunci API dari config/services.php (.env).
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
                        'responseSchema' => $this->skema(),
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
            // Token "berpikir" model 2.5 ditagih sebagai keluaran.
            'token_keluar' => isset($pemakaian['candidatesTokenCount']) || isset($pemakaian['thoughtsTokenCount'])
                ? ($pemakaian['candidatesTokenCount'] ?? 0) + ($pemakaian['thoughtsTokenCount'] ?? 0)
                : null,
        ];
    }

    /**
     * Skema JSON keluaran: satu baris per tanggal yang punya cetakan jam.
     *
     * @return array<string, mixed>
     */
    private function skema(): array
    {
        return [
            'type' => 'OBJECT',
            'properties' => [
                'baris' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'tanggal' => ['type' => 'INTEGER', 'description' => 'Tanggal (1–31) tempat jam ini paling masuk akal.'],
                            'jam_masuk' => ['type' => 'STRING', 'nullable' => true, 'description' => 'Pagi Masuk, format HH:MM 24 jam, atau null.'],
                            'jam_keluar' => ['type' => 'STRING', 'nullable' => true, 'description' => 'Siang Keluar, format HH:MM 24 jam, atau null.'],
                            'keyakinan' => ['type' => 'NUMBER', 'description' => 'Keyakinan 0–1 bahwa tanggal dan jam baris ini benar.'],
                            'catatan' => ['type' => 'STRING', 'nullable' => true, 'description' => 'Alasan singkat bila ragu, mis. cetakan meleset.'],
                        ],
                        'required' => ['tanggal', 'keyakinan'],
                    ],
                ],
            ],
            'required' => ['baris'],
        ];
    }
}
