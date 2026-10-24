<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Kelompok (bobot %) dan aspek penilaian per kelompok.
 */
class SimpanAspekPenilaianRequest extends FormRequest
{
    /**
     * Akses sudah dibatasi oleh middleware route (khusus Super Admin).
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
            'kelompok' => ['required', 'array'],
            'kelompok.*.nama' => ['required', 'string', 'max:100'],
            'kelompok.*.bobot' => ['required', 'integer', 'min:0', 'max:100'],

            'aspek' => ['nullable', 'array'],
            'aspek.*.nama' => ['required', 'string', 'max:255'],
            'aspek.*.aktif' => ['nullable', 'boolean'],

            'baru' => ['nullable', 'array'],
            'baru.*' => ['nullable', 'string', 'max:255'],

            'kelompok_baru.nama' => ['nullable', 'required_with:kelompok_baru.bobot', 'string', 'max:100'],
            'kelompok_baru.bobot' => ['nullable', 'required_with:kelompok_baru.nama', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Total bobot kelompok harus 100%, dan kelompok berbobot harus punya aspek aktif.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $total = collect($this->input('kelompok', []))->sum(fn (array $kelompok) => (int) $kelompok['bobot'])
                    + (int) $this->input('kelompok_baru.bobot', 0);

                if ($total !== 100) {
                    $validator->errors()->add('total_bobot', "Total bobot kelompok harus 100%, sekarang {$total}%.");
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kelompok.*.nama' => 'nama kelompok',
            'kelompok.*.bobot' => 'bobot kelompok',
            'aspek.*.nama' => 'nama aspek',
            'baru.*' => 'aspek baru',
            'kelompok_baru.nama' => 'nama kelompok baru',
            'kelompok_baru.bobot' => 'bobot kelompok baru',
        ];
    }
}
