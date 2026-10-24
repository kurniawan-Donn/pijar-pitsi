<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    /**
     * Akses sudah dibatasi oleh middleware route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi simpan / ubah banner utama.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'tombol_teks' => ['nullable', 'string', 'max:100'],
            // Dipakai sebagai href di website: hanya http(s), path relatif, atau anchor.
            // Mencegah "javascript:..." (XSS).
            'tombol_link' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/|\/(?!\/)|#)/i'],
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
            'judul.required' => 'Judul banner wajib diisi.',
            'judul.max' => 'Judul banner maksimal 255 karakter.',
            'gambar.image' => 'File banner harus berupa gambar.',
            'gambar.mimes' => 'Gambar banner harus berformat JPG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar banner maksimal 2 MB.',
            'tombol_teks.max' => 'Teks tombol maksimal 100 karakter.',
            'tombol_link.max' => 'Link tombol maksimal 255 karakter.',
            'tombol_link.regex' => 'Link tombol harus diawali http://, https://, / (halaman di website ini), atau # (bagian halaman).',
        ];
    }
}
