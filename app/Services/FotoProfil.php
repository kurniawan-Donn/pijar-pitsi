<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Foto profil pengguna: dipotong tengah menjadi persegi, diperkecil, lalu
 * disimpan ulang sebagai JPG di disk privat. Menyimpan ulang sekaligus
 * membuang data EXIF (lokasi, kamera) dari foto asli.
 */
class FotoProfil
{
    public const UKURAN = 400;

    public const FOLDER = 'foto-profil';

    /**
     * Aturan validasi file unggahan.
     *
     * @return array<int, string>
     */
    public static function aturan(): array
    {
        return ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048', 'dimensions:min_width=100,min_height=100'];
    }

    /**
     * Pesan validasi.
     *
     * @return array<string, string>
     */
    public static function pesan(string $field = 'foto'): array
    {
        return [
            "{$field}.required" => 'Pilih foto yang akan diunggah.',
            "{$field}.mimes" => 'Foto harus berformat JPG atau PNG.',
            "{$field}.max" => 'Ukuran foto maksimal 2 MB.',
            "{$field}.dimensions" => 'Foto minimal 100 × 100 piksel.',
        ];
    }

    /**
     * Simpan foto baru dan hapus foto lama. Mengembalikan path file baru.
     */
    public function simpan(User $user, UploadedFile $file, string $field = 'foto'): string
    {
        $gambar = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($gambar === false) {
            throw ValidationException::withMessages([$field => 'File foto tidak dapat dibaca. Coba foto lain.']);
        }

        $gambar = $this->putarSesuaiExif($gambar, $file);
        $persegi = $this->potongPersegi($gambar);

        ob_start();
        imagejpeg($persegi, null, 85);
        $isi = (string) ob_get_clean();

        $path = self::FOLDER.'/'.$user->id.'-'.Str::random(12).'.jpg';
        Storage::disk('local')->put($path, $isi);

        $lama = $user->foto_path;
        $user->forceFill(['foto_path' => $path])->save();

        if ($lama) {
            Storage::disk('local')->delete($lama);
        }

        return $path;
    }

    public function hapus(User $user): void
    {
        if ($user->foto_path) {
            Storage::disk('local')->delete($user->foto_path);
        }

        $user->forceFill(['foto_path' => null])->save();
    }

    /**
     * Foto dari kamera HP sering disimpan miring dengan penanda orientasi EXIF.
     */
    private function putarSesuaiExif(\GdImage $gambar, UploadedFile $file): \GdImage
    {
        if (! function_exists('exif_read_data') || ! in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg'], true)) {
            return $gambar;
        }

        $orientasi = (int) (@exif_read_data($file->getRealPath())['Orientation'] ?? 1);

        $sudut = match ($orientasi) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        return $sudut === 0 ? $gambar : (imagerotate($gambar, $sudut, 0) ?: $gambar);
    }

    private function potongPersegi(\GdImage $gambar): \GdImage
    {
        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);
        $sisi = min($lebar, $tinggi);

        $hasil = imagecreatetruecolor(self::UKURAN, self::UKURAN);

        // Latar putih untuk PNG transparan (JPG tidak punya transparansi).
        imagefill($hasil, 0, 0, (int) imagecolorallocate($hasil, 255, 255, 255));

        imagecopyresampled(
            $hasil,
            $gambar,
            0,
            0,
            intdiv($lebar - $sisi, 2),
            intdiv($tinggi - $sisi, 2),
            self::UKURAN,
            self::UKURAN,
            $sisi,
            $sisi
        );

        return $hasil;
    }
}
