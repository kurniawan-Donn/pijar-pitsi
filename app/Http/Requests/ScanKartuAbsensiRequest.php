<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Unggah foto kartu absensi untuk dibaca (bulan, tahun, sisi dipilih peserta).
 */
class ScanKartuAbsensiRequest extends FormRequest
{
    /**
     * Akses dijaga middleware role di route; jendela & batas harian dicek di controller.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Pilihan kartu dikirim sebagai "sisi|tahun|bulan" dari daftar jendela yang aktif.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'kartu' => ['required', 'string', 'regex:/^(1-15|16-31)\|\d{4}\|\d{1,2}$/'],
            'foto' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240', 'dimensions:min_width=400,min_height=400'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kartu.required' => 'Pilih kartu (bulan dan sisi) yang akan di-scan.',
            'kartu.regex' => 'Pilihan kartu tidak dikenali.',
            'foto.required' => 'Ambil atau pilih foto kartu absensi.',
            'foto.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 10 MB.',
            'foto.dimensions' => 'Foto terlalu kecil; pastikan seluruh kartu terlihat jelas.',
        ];
    }

    /**
     * @return array{sisi: string, tahun: int, bulan: int}
     */
    public function kartu(): array
    {
        [$sisi, $tahun, $bulan] = explode('|', $this->validated('kartu'));

        return ['sisi' => $sisi, 'tahun' => (int) $tahun, 'bulan' => (int) $bulan];
    }
}
