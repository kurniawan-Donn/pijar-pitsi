<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Feedback magang: semua pertanyaan skala, terbuka, rekomendasi, evaluasi,
 * dan pernyataan wajib diisi.
 */
class SimpanFeedbackRequest extends FormRequest
{
    /**
     * Akses sudah dibatasi middleware route (role:peserta + peserta.aktif).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $aturan = [
            'skor' => ['required', 'array'],
            'rekomendasi' => ['required', Rule::in(array_keys(config('feedback.rekomendasi')))],
            'evaluasi_keseluruhan' => ['required', 'string', 'max:2000'],
            'pernyataan' => ['accepted'],
        ];

        foreach (array_keys(config('feedback.pertanyaan_skala')) as $nomor) {
            $aturan["skor.{$nomor}"] = ['required', 'integer', 'between:1,5'];
        }

        foreach (array_keys(config('feedback.pertanyaan_terbuka')) as $kolom) {
            $aturan[$kolom] = ['required', 'string', 'max:2000'];
        }

        return $aturan;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'skor.*.required' => 'Pilih nilai untuk pertanyaan nomor :position.',
            'skor.*.between' => 'Nilai harus 1 sampai 5.',
            'rekomendasi.required' => 'Pilih kesediaan merekomendasikan program magang.',
            'pernyataan.accepted' => 'Centang pernyataan kejujuran sebelum mengirim feedback.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'paling_disukai' => 'Hal yang paling disukai',
            'kendala' => 'Kendala',
            'perlu_diperbaiki' => 'Hal yang perlu diperbaiki',
            'saran' => 'Saran',
            'evaluasi_keseluruhan' => 'Evaluasi keseluruhan',
        ];
    }

    /**
     * Skor hanya untuk nomor pertanyaan yang ada di config.
     *
     * @return array<int, int>
     */
    public function skor(): array
    {
        return collect(config('feedback.pertanyaan_skala'))
            ->keys()
            ->mapWithKeys(fn (int $nomor) => [$nomor => (int) $this->input("skor.{$nomor}")])
            ->all();
    }
}
