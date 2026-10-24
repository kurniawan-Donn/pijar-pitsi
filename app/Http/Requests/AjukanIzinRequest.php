<?php

namespace App\Http\Requests;

use App\Models\IzinPeserta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Pengajuan izin / sakit untuk satu rentang tanggal dengan satu bukti.
 * Bukti wajib untuk sakit (mis. surat dokter), opsional untuk izin.
 */
class AjukanIzinRequest extends FormRequest
{
    public const MAKS_HARI = 31;

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
        return [
            'jenis' => ['required', Rule::in(array_keys(IzinPeserta::JENIS))],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['required', 'string', 'max:1000'],
            'bukti' => ['nullable', 'required_if:jenis,sakit', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bukti.required_if' => 'Bukti (mis. surat dokter) wajib diunggah untuk sakit.',
            'bukti.mimes' => 'Bukti harus berupa JPG, PNG, atau PDF.',
            'bukti.max' => 'Ukuran bukti maksimal 2 MB.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'jenis' => 'jenis',
            'tanggal_mulai' => 'tanggal mulai',
            'tanggal_selesai' => 'tanggal selesai',
        ];
    }
}
