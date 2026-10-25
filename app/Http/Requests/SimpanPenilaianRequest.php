<?php

namespace App\Http\Requests;

use App\Models\AspekPenilaian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SimpanPenilaianRequest extends FormRequest
{
    /**
     * Batas karakter tiap kolom kesimpulan, agar lembar penilaian tetap 1 halaman A4.
     */
    public const MAKS_KESIMPULAN = 600;

    /**
     * Batas baris (paragraf) tiap kolom kesimpulan; baris baru ikut tercetak di PDF.
     */
    public const MAKS_BARIS_KESIMPULAN = 5;

    /**
     * Kolom kesimpulan beserta labelnya untuk pesan validasi.
     *
     * @var array<string, string>
     */
    public const KOLOM_KESIMPULAN = [
        'kekuatan' => 'Kekuatan dan keunggulan',
        'aspek_perbaikan' => 'Aspek perbaikan',
        'langkah_perbaikan' => 'Langkah perbaikan',
    ];

    /**
     * Akses sudah dibatasi oleh middleware route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Samakan baris baru (CRLF dari browser) menjadi LF agar hitungan karakter sama dengan form.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(collect(self::KOLOM_KESIMPULAN)
            ->keys()
            ->filter(fn (string $kolom) => is_string($this->input($kolom)))
            ->mapWithKeys(fn (string $kolom) => [$kolom => str_replace("\r\n", "\n", $this->input($kolom))])
            ->all());
    }

    /**
     * Aturan validasi nilai per aspek.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nilai' => ['required', 'array'],
            'nilai.*' => ['required', 'integer', 'min:1', 'max:100'],
            'kekuatan' => ['nullable', 'string', 'max:'.self::MAKS_KESIMPULAN],
            'aspek_perbaikan' => ['nullable', 'string', 'max:'.self::MAKS_KESIMPULAN],
            'langkah_perbaikan' => ['nullable', 'string', 'max:'.self::MAKS_KESIMPULAN],
        ];
    }

    /**
     * Kolom kesimpulan tidak melebihi batas baris; semua aspek aktif wajib dinilai.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $dinilai = array_map('intval', array_keys((array) $this->input('nilai', [])));

                foreach (self::KOLOM_KESIMPULAN as $kolom => $label) {
                    $baris = preg_split('/\R/', trim((string) $this->input($kolom, '')));

                    if (count($baris) > self::MAKS_BARIS_KESIMPULAN) {
                        $validator->errors()->add($kolom, "{$label} maksimal ".self::MAKS_BARIS_KESIMPULAN.' baris agar lembar penilaian muat 1 halaman.');
                    }
                }

                foreach (AspekPenilaian::aktif()->whereNotNull('kelompok_penilaian_id')->get() as $aspek) {
                    if (! in_array($aspek->id, $dinilai, true)) {
                        $validator->errors()->add("nilai.{$aspek->id}", "Nilai {$aspek->nama} wajib diisi.");
                    }
                }
            },
        ];
    }

    /**
     * Pesan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nilai.required' => 'Nilai aspek wajib diisi.',
            'nilai.*.required' => 'Nilai wajib diisi.',
            'nilai.*.integer' => 'Nilai harus berupa angka bulat.',
            'nilai.*.min' => 'Nilai minimal 1.',
            'nilai.*.max' => 'Nilai maksimal 100.',
            '*.max' => 'Isian maksimal :max karakter.',
        ];
    }
}
