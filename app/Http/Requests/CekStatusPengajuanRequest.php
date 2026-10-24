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
            'status_nim' => trim((string) $this->input('status_nim')),
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
            'status_nim' => ['required', 'string', 'max:30'],
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
            'status_nim.required' => 'NIM wajib diisi.',
            'status_email.required' => 'Email wajib diisi.',
            'status_email.email' => 'Format email tidak valid.',
        ];
    }
}
