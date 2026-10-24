<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PesertaMagangRequest extends FormRequest
{
    /**
     * Akses sudah dibatasi oleh middleware route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi tambah / edit peserta magang.
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'nim' => ['nullable', 'string', 'max:50'],
            'asal_kampus' => ['nullable', 'string', 'max:255'],
            'program_studi' => ['nullable', 'string', 'max:255'],
            'periode_mulai' => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'pembimbing' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,selesai'],
            'google_sheet_url' => ['nullable', 'url:https', 'max:2048', 'regex:#^https://docs\.google\.com/spreadsheets/d/[A-Za-z0-9_-]+#'],
        ];
    }

    /**
     * Pesan validasi.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama peserta wajib diisi.',
            'periode_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'status.in' => 'Status peserta tidak valid.',
            'google_sheet_url.url' => 'Link Google Sheets tidak valid.',
            'google_sheet_url.regex' => 'Gunakan link spreadsheet Google Sheets (https://docs.google.com/spreadsheets/d/...).',
        ];
    }
}
