<?php

namespace App\Http\Requests;

use App\Models\AspekPenilaian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SimpanPenilaianRequest extends FormRequest
{
    /**
     * Akses sudah dibatasi oleh middleware route.
     */
    public function authorize(): bool
    {
        return true;
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
            'nilai.*' => ['required', 'integer', 'min:0', 'max:100'],
            'catatan_pembimbing' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Semua aspek aktif wajib dinilai.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $dinilai = array_map('intval', array_keys((array) $this->input('nilai', [])));

                foreach (AspekPenilaian::aktif()->get() as $aspek) {
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
            'nilai.*.min' => 'Nilai minimal 0.',
            'nilai.*.max' => 'Nilai maksimal 100.',
            'catatan_pembimbing.max' => 'Catatan maksimal 2000 karakter.',
        ];
    }
}
