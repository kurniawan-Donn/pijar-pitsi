<?php

namespace App\Http\Requests;

use App\Services\FotoDokumentasi;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Data dokumentasi kegiatan dari form admin (tambah & ubah).
 */
class SimpanDokumentasiRequest extends FormRequest
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
        return [
            'judul' => ['required', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:300'],
            'tanggal_kegiatan' => ['nullable', 'date', 'before_or_equal:today'],
            // Foto wajib saat tambah; saat ubah boleh dikosongkan (foto lama dipakai).
            'foto' => FotoDokumentasi::aturan(wajib: $this->route('dokumentasi') === null),
            'tampil' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'foto.required' => 'Pilih foto dokumentasi.',
            'foto.mimes' => 'Foto harus berformat JPG atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
            'foto.dimensions' => 'Foto minimal 300 × 200 piksel.',
            'tanggal_kegiatan.before_or_equal' => 'Tanggal kegiatan tidak boleh di masa depan.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul',
            'keterangan' => 'keterangan',
            'tanggal_kegiatan' => 'tanggal kegiatan',
        ];
    }

    /**
     * Kolom yang disimpan ke model.
     *
     * @return array{judul: string, keterangan: ?string, tanggal_kegiatan: ?string, tampil: bool}
     */
    public function dataDokumentasi(): array
    {
        return [
            'judul' => trim($this->validated('judul')),
            'keterangan' => filled($this->validated('keterangan')) ? trim($this->validated('keterangan')) : null,
            'tanggal_kegiatan' => $this->validated('tanggal_kegiatan'),
            'tampil' => $this->boolean('tampil'),
        ];
    }
}
