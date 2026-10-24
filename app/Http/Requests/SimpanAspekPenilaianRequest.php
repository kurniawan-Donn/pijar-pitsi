<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
     * Aturan validasi daftar aspek penilaian.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'aspek' => ['nullable', 'array'],
            'aspek.*.nama' => ['required', 'string', 'max:100'],
            'aspek.*.bobot' => ['required', 'integer', 'min:1', 'max:100'],
            'aspek.*.aktif' => ['nullable', 'boolean'],

            'baru.nama' => ['nullable', 'required_with:baru.bobot', 'string', 'max:100'],
            'baru.bobot' => ['nullable', 'required_with:baru.nama', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Total bobot aspek aktif harus tepat 100%.
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

                $total = collect($this->input('aspek', []))
                    ->filter(fn (array $aspek) => (bool) ($aspek['aktif'] ?? false))
                    ->sum(fn (array $aspek) => (int) $aspek['bobot']);

                $total += (int) $this->input('baru.bobot', 0);

                if ($total !== 100) {
                    $validator->errors()->add('total_bobot', "Total bobot aspek aktif harus 100%, sekarang {$total}%.");
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
            'aspek.*.nama.required' => 'Nama aspek wajib diisi.',
            'aspek.*.bobot.required' => 'Bobot aspek wajib diisi.',
            'aspek.*.bobot.integer' => 'Bobot harus berupa angka bulat.',
            'aspek.*.bobot.min' => 'Bobot minimal 1%.',
            'aspek.*.bobot.max' => 'Bobot maksimal 100%.',
            'baru.nama.required_with' => 'Nama aspek baru wajib diisi.',
            'baru.bobot.required_with' => 'Bobot aspek baru wajib diisi.',
            'baru.bobot.min' => 'Bobot minimal 1%.',
        ];
    }
}
