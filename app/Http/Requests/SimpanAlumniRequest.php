<?php

namespace App\Http\Requests;

use App\Models\Alumni;
use App\Models\PesertaMagang;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Data alumni dari form admin (tambah manual, tambah dari peserta, ubah).
 */
class SimpanAlumniRequest extends FormRequest
{
    /**
     * Akses dijaga middleware role di route.
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
        /** @var Alumni|null $alumni */
        $alumni = $this->route('alumni');
        $dariPeserta = $alumni ? $alumni->dariPeserta() : $this->filled('peserta_magang_id');

        return [
            // peserta_magang_id hanya saat tambah; tidak bisa diganti setelahnya.
            'peserta_magang_id' => $alumni ? ['prohibited'] : ['nullable', 'integer', Rule::exists('peserta_magangs', 'id'), Rule::unique('alumnis', 'peserta_magang_id')],
            'nama' => ['required', 'string', 'max:255'],
            'universitas' => ['required', 'string', 'max:255'],
            'program_studi' => ['nullable', 'string', 'max:255'],
            'divisi' => ['nullable', 'string', 'max:255'],
            'periode' => ['nullable', 'string', 'max:100'],
            // Rating alumni dari peserta selalu dari feedback.
            'rating' => $dariPeserta ? ['prohibited'] : ['nullable', 'integer', 'between:1,5'],
            'judul_project' => ['required', 'string', 'max:150'],
            'deskripsi_project' => ['required', 'string', 'max:3000'],
            'testimoni' => ['nullable', 'string', 'max:1500'],
            'tampil' => ['nullable', 'boolean'],
            'foto' => $dariPeserta ? ['prohibited'] : ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048', 'dimensions:min_width=100,min_height=100'],
            'hapus_foto' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Peserta yang dipilih harus sudah selesai dan bersedia ditampilkan.
     *
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->filled('peserta_magang_id') || $validator->errors()->has('peserta_magang_id')) {
                    return;
                }

                $peserta = PesertaMagang::with('feedback')->find($this->integer('peserta_magang_id'));

                if ($peserta?->status !== 'selesai' || ! $peserta->feedback?->bersedia_tampil_alumni) {
                    $validator->errors()->add('peserta_magang_id', 'Hanya peserta yang sudah selesai dan bersedia ditampilkan sebagai alumni yang bisa dipilih.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'peserta_magang_id.unique' => 'Peserta ini sudah terdaftar sebagai alumni.',
            'rating.prohibited' => 'Rating alumni dari peserta diambil dari feedback dan tidak bisa diubah.',
            'foto.prohibited' => 'Foto alumni dari peserta memakai foto profil peserta.',
            'foto.mimes' => 'Foto harus berformat JPG atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'foto.dimensions' => 'Foto minimal 100 × 100 piksel.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama',
            'universitas' => 'asal universitas',
            'program_studi' => 'program studi',
            'divisi' => 'divisi penempatan',
            'periode' => 'periode magang',
            'judul_project' => 'judul project',
            'deskripsi_project' => 'deskripsi project',
            'testimoni' => 'testimoni',
        ];
    }

    /**
     * Kolom yang disimpan ke model.
     *
     * @return array<string, mixed>
     */
    public function dataAlumni(): array
    {
        return collect($this->validated())
            ->except(['foto', 'hapus_foto'])
            ->merge(['tampil' => $this->boolean('tampil')])
            ->all();
    }
}
