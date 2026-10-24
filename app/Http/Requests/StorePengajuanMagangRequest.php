<?php

namespace App\Http\Requests;

use App\Models\PengajuanMagang;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePengajuanMagangRequest extends FormRequest
{
    /**
     * Form pendaftaran terbuka untuk publik.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Kembali ke bagian form pendaftaran bila validasi gagal.
     */
    protected function getRedirectUrl(): string
    {
        return route('home').'#daftar';
    }

    /**
     * Rapikan input sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nim' => trim((string) $this->input('nim')),
            'email' => strtolower(trim((string) $this->input('email'))),
        ]);
    }

    /**
     * Aturan validasi pendaftaran magang.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Data diri
            'nama' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9.\-]+$/'],
            'email' => [
                'required',
                'email',
                'max:255',
                // Email boleh dipakai ulang oleh NIM yang sama, tidak oleh NIM lain.
                Rule::unique('mahasiswas', 'email')->where(
                    fn ($query) => $query->where('nim', '!=', $this->input('nim'))
                ),
            ],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],

            // Data akademik
            'asal_kampus' => ['required', 'string', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
            'fakultas' => ['nullable', 'string', 'max:255'],

            // Rencana magang
            'posisi' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'persetujuan' => ['accepted'],
        ];
    }

    /**
     * Tolak pengajuan baru bila NIM masih punya pengajuan yang sedang diproses.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->has('nim')) {
                    return;
                }

                $masihDiproses = PengajuanMagang::query()
                    ->where('status', PengajuanMagang::STATUS_MENUNGGU)
                    ->whereHas('mahasiswa', fn ($query) => $query->where('nim', $this->input('nim')))
                    ->exists();

                if ($masihDiproses) {
                    $validator->errors()->add(
                        'nim',
                        'Masih ada pengajuan dengan NIM ini yang sedang diproses. Silakan cek status pengajuan.'
                    );
                }
            },
        ];
    }

    /**
     * Nama field dalam bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama' => 'Nama lengkap',
            'nim' => 'NIM',
            'email' => 'Email',
            'no_hp' => 'Nomor HP/WhatsApp',
            'asal_kampus' => 'Asal kampus',
            'program_studi' => 'Program studi',
            'fakultas' => 'Fakultas',
            'posisi' => 'Bidang/posisi magang',
            'tanggal_mulai' => 'Tanggal mulai',
            'tanggal_selesai' => 'Tanggal selesai',
            'keterangan' => 'Motivasi/keterangan',
            'dokumen' => 'Surat pengantar',
            'persetujuan' => 'Pernyataan kebenaran data',
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
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'email' => 'Format :attribute tidak valid.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'email.unique' => 'Email ini sudah terdaftar untuk NIM lain.',
            'nim.regex' => 'NIM hanya boleh berisi huruf, angka, titik, atau tanda hubung.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka, spasi, + atau - (8–20 karakter).',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'dokumen.required' => 'Surat pengantar wajib diunggah.',
            'dokumen.file' => 'Surat pengantar harus berupa file.',
            'dokumen.mimes' => 'Surat pengantar harus berformat PDF, JPG, atau PNG.',
            'dokumen.max' => 'Ukuran surat pengantar maksimal 2 MB.',
            'persetujuan.accepted' => 'Centang pernyataan bahwa data yang diisi sudah benar.',
        ];
    }
}
