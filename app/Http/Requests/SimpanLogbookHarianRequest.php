<?php

namespace App\Http\Requests;

use App\Models\LogbookHarian;
use App\Services\RekapAbsensi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Isian logbook satu hari oleh peserta (Hadir atau Libur).
 * Izin/sakit diajukan lewat form izin (bisa satu rentang tanggal).
 */
class SimpanLogbookHarianRequest extends FormRequest
{
    public const MAKS_BUKTI = 3;

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
            'kehadiran' => ['required', Rule::in([LogbookHarian::HADIR, LogbookHarian::LIBUR])],
            'pagi_masuk' => ['nullable', 'date_format:H:i'],
            'pagi_keluar' => ['nullable', 'date_format:H:i'],
            'siang_masuk' => ['nullable', 'date_format:H:i'],
            'siang_keluar' => ['nullable', 'date_format:H:i'],
            'activity' => ['nullable', 'required_if:kehadiran,hadir', 'string', 'max:2000'],
            'output' => ['nullable', 'string', 'max:2000'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'bukti' => ['nullable', 'array', 'max:'.self::MAKS_BUKTI],
            'bukti.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'hapus_bukti' => ['nullable', 'array'],
            'hapus_bukti.*' => ['integer'],
        ];
    }

    /**
     * Jam harus membentuk hari penuh atau setengah hari dan berurutan.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty() || $this->input('kehadiran') !== LogbookHarian::HADIR) {
                    return;
                }

                $jam = $this->only(LogbookHarian::KOLOM_JAM);

                if (RekapAbsensi::jenisKehadiran($jam) === 'tidak_lengkap') {
                    $validator->errors()->add('pagi_masuk', 'Isi jam pagi masuk dan siang keluar (hari penuh), atau satu sesi lengkap: pagi masuk–keluar atau siang masuk–keluar (setengah hari).');

                    return;
                }

                $urutan = array_values(array_filter($jam));

                for ($i = 1; $i < count($urutan); $i++) {
                    if ($urutan[$i] < $urutan[$i - 1]) {
                        $validator->errors()->add('siang_keluar', 'Urutan jam tidak masuk akal: pagi masuk, pagi keluar, siang masuk, lalu siang keluar.');

                        return;
                    }
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
            'activity.required_if' => 'Activity wajib diisi untuk hari hadir.',
            'bukti.max' => 'Maksimal '.self::MAKS_BUKTI.' file bukti per hari.',
            'bukti.*.mimes' => 'Bukti harus berupa JPG, PNG, atau PDF.',
            'bukti.*.max' => 'Ukuran setiap bukti maksimal 2 MB.',
            '*.date_format' => 'Format jam harus JJ:MM.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'pagi_masuk' => 'jam pagi masuk',
            'pagi_keluar' => 'jam pagi keluar',
            'siang_masuk' => 'jam siang masuk',
            'siang_keluar' => 'jam siang keluar',
            'bukti.*' => 'bukti',
        ];
    }
}
