<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CekStatusPengajuanRequest extends FormRequest
{
    /**
     * Cek status terbuka untuk publik.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Kembali ke bagian cek status bila validasi gagal.
     */
    protected function getRedirectUrl(): string
    {
        return route('home').'#status';
    }

    /**
     * Rapikan input sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status_kode' => strtoupper(trim((string) $this->input('status_kode'))),
            'status_email' => strtolower(trim((string) $this->input('status_email'))),
        ]);
    }

    /**
     * Aturan validasi cek status.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status_kode' => ['required', 'string', 'max:20', 'regex:/^MG-?\d{1,10}$/'],
            'status_email' => ['required', 'email', 'max:255'],
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
            'status_kode.required' => 'Kode pengajuan wajib diisi.',
            'status_kode.regex' => 'Format kode pengajuan seperti MG-000012.',
            'status_email.required' => 'Email wajib diisi.',
            'status_email.email' => 'Format email tidak valid.',
        ];
    }
}
