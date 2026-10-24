<?php

namespace App\Http\Requests;

use App\Models\PengajuanMagang;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusPengajuanRequest extends FormRequest
{
    /**
     * Akses sudah dibatasi oleh middleware route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi ubah status pengajuan.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(PengajuanMagang::STATUS_LABELS))],
            'catatan_admin' => [
                Rule::requiredIf($this->input('status') === PengajuanMagang::STATUS_DITOLAK),
                'nullable',
                'string',
                'max:1000',
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
            'status.required' => 'Status pengajuan wajib dipilih.',
            'status.in' => 'Status pengajuan tidak valid.',
            'catatan_admin.required' => 'Catatan wajib diisi bila pengajuan ditolak, agar pendaftar tahu alasannya.',
            'catatan_admin.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
