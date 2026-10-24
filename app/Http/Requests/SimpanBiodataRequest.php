<?php

namespace App\Http\Requests;

use App\Models\BiodataPeserta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Simpan draft (format saja) atau kirim biodata (semua field wajib harus terisi).
 */
class SimpanBiodataRequest extends FormRequest
{
    /**
     * Maksimal baris untuk daftar keterampilan / bahasa / riwayat kesehatan.
     */
    private const MAKS_BARIS = 10;

    private const POLA_TELEPON = '/^\+?[0-9]{8,15}$/';

    /**
     * Akses sudah dibatasi middleware route (role:peserta + peserta.aktif).
     */
    public function authorize(): bool
    {
        return true;
    }

    public function dikirim(): bool
    {
        return $this->input('aksi') === 'kirim';
    }

    /**
     * Bersihkan baris kosong pada daftar dan rapikan angka sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $daftar = [];

        foreach (['keterampilan', 'bahasa', 'riwayat_kesehatan'] as $field) {
            $daftar[$field] = array_values(array_filter(
                array_map(fn ($baris) => trim((string) $baris), (array) $this->input($field, [])),
                fn (string $baris) => $baris !== ''
            ));
        }

        $this->merge($daftar + [
            'ipk' => $this->filled('ipk') ? str_replace(',', '.', (string) $this->input('ipk')) : null,
            'nik' => $this->filled('nik') ? preg_replace('/\s+/', '', (string) $this->input('nik')) : null,
            'no_hp' => $this->filled('no_hp') ? preg_replace('/[\s\-]+/', '', (string) $this->input('no_hp')) : null,
            'dosen_pembimbing_kontak' => $this->filled('dosen_pembimbing_kontak')
                ? preg_replace('/[\s\-]+/', '', (string) $this->input('dosen_pembimbing_kontak'))
                : null,
            'pernyataan_setuju' => $this->boolean('pernyataan_setuju'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $wajib = $this->dikirim() ? 'required' : 'nullable';

        return [
            'aksi' => ['required', Rule::in(['simpan', 'kirim'])],

            'nama_lengkap' => [$wajib, 'string', 'max:255'],
            'nama_panggilan' => ['nullable', 'string', 'max:100'],
            'jenis_kelamin' => [$wajib, Rule::in(array_keys(BiodataPeserta::JENIS_KELAMIN))],
            'tempat_lahir' => [$wajib, 'string', 'max:100'],
            'tanggal_lahir' => [$wajib, 'date', 'after:1950-01-01', 'before:today'],
            'nik' => [$wajib, 'digits:16'],
            'alamat_ktp' => [$wajib, 'string', 'max:500'],
            'alamat_domisili' => [$wajib, 'string', 'max:500'],
            'tautan_maps_domisili' => ['nullable', 'url', 'max:500'],
            'no_hp' => [$wajib, 'regex:'.self::POLA_TELEPON],
            'email' => [$wajib, 'email', 'max:255'],
            'tautan_portofolio' => ['nullable', 'url', 'max:500'],
            'tautan_instagram' => ['nullable', 'url', 'max:500'],

            'perguruan_tinggi' => [$wajib, 'string', 'max:255'],
            'fakultas_jurusan' => [$wajib, 'string', 'max:255'],
            'nim' => [$wajib, 'string', 'max:50'],
            'semester' => [$wajib, 'integer', 'min:1', 'max:14'],
            'ipk' => [$wajib, 'numeric', 'min:0', 'max:4'],
            'dosen_pembimbing_nama' => [$wajib, 'string', 'max:255'],
            'dosen_pembimbing_kontak' => [$wajib, 'regex:'.self::POLA_TELEPON],
            'dosen_pembimbing_email' => ['nullable', 'email', 'max:255'],

            'keterampilan' => [$wajib, 'array', 'max:'.self::MAKS_BARIS],
            'keterampilan.*' => ['string', 'max:255'],
            'bahasa' => [$wajib, 'array', 'max:'.self::MAKS_BARIS],
            'bahasa.*' => ['string', 'max:255'],
            'riwayat_kesehatan' => ['nullable', 'array', 'max:'.self::MAKS_BARIS],
            'riwayat_kesehatan.*' => ['string', 'max:255'],

            'no_rekening' => [$wajib, 'regex:/^[0-9][0-9\-\s]{4,29}$/'],
            'nama_bank' => [$wajib, 'string', 'max:100'],
            'atas_nama_rekening' => [$wajib, 'string', 'max:255'],

            'pernyataan_setuju' => $this->dikirim() ? ['accepted'] : ['boolean'],
            'pernyataan_tempat' => [$wajib, 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi sebelum biodata dikirim.',
            'keterampilan.required' => 'Isi minimal satu keterampilan sebelum biodata dikirim.',
            'bahasa.required' => 'Isi minimal satu bahasa sebelum biodata dikirim.',
            'pernyataan_setuju.accepted' => 'Centang pernyataan kebenaran data sebelum mengirim biodata.',
            'no_hp.regex' => 'No. WhatsApp / HP hanya berisi 8–15 angka (boleh diawali +).',
            'dosen_pembimbing_kontak.regex' => 'Kontak dosen pembimbing hanya berisi 8–15 angka (boleh diawali +).',
            'no_rekening.regex' => 'Nomor rekening hanya berisi angka.',
            'nik.digits' => 'NIK harus 16 digit angka.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return BiodataPeserta::FIELD_WAJIB + [
            'nama_panggilan' => 'Nama panggilan',
            'tautan_maps_domisili' => 'Tautan Maps domisili',
            'tautan_portofolio' => 'Tautan LinkedIn / portofolio',
            'tautan_instagram' => 'Tautan Instagram',
            'dosen_pembimbing_email' => 'Email dosen pembimbing',
            'keterampilan.*' => 'Keterampilan',
            'bahasa.*' => 'Bahasa',
            'riwayat_kesehatan' => 'Riwayat penyakit / alergi',
            'riwayat_kesehatan.*' => 'Riwayat penyakit / alergi',
            'pernyataan_tempat' => 'Tempat pernyataan',
        ];
    }

    /**
     * Data yang boleh diisi peserta (tanpa field program & status).
     *
     * @return array<string, mixed>
     */
    public function dataBiodata(): array
    {
        return collect($this->validated())->only(BiodataPeserta::FIELD_PESERTA)->all();
    }
}
