<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifikasiLogbookRequest extends FormRequest
{
    /**
     * Akses sudah dibatasi oleh middleware route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi verifikasi logbook (kolom Action).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'bulan' => ['required', 'date_format:Y-m'],
            'action' => ['required', Rule::in(config('absensi.action_options'))],
            'baris' => ['required', 'array', 'min:1'],
            'baris.*' => [
                'integer',
                'min:'.config('absensi.first_data_row'),
                'max:'.config('absensi.last_row'),
            ],
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
            'bulan.required' => 'Bulan logbook tidak ditemukan.',
            'bulan.date_format' => 'Bulan logbook tidak valid.',
            'action.required' => 'Pilih status verifikasi.',
            'action.in' => 'Status verifikasi tidak valid.',
            'baris.required' => 'Pilih minimal satu hari logbook.',
            'baris.min' => 'Pilih minimal satu hari logbook.',
            'baris.*' => 'Baris logbook tidak valid.',
        ];
    }
}
