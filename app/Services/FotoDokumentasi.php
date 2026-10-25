<?php

namespace App\Services;

use App\Models\Dokumentasi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Foto dokumentasi di disk "public": versi besar (lightbox) dan thumbnail (galeri).
 * Foto disimpan ulang sebagai JPG sehingga data EXIF (lokasi, kamera) terbuang,
 * dan diputar sesuai orientasi kamera HP.
 */
class FotoDokumentasi
{
    public const SISI_BESAR = 1600;

    public const SISI_THUMBNAIL = 640;

    /**
     * Aturan validasi file unggahan.
     *
     * @return array<int, string>
     */
    public static function aturan(bool $wajib): array
    {
        return [$wajib ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120', 'dimensions:min_width=300,min_height=200'];
    }

    /**
     * Simpan foto baru untuk dokumentasi (mengganti foto lama bila ada).
     */
    public function simpan(Dokumentasi $dokumentasi, UploadedFile $file): void
    {
        $gambar = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($gambar === false) {
            throw ValidationException::withMessages(['foto' => 'File foto tidak dapat dibaca. Coba foto lain.']);
        }

        $gambar = $this->putarSesuaiExif($gambar, $file);
        $dasar = Dokumentasi::FOLDER.'/'.Str::random(20);
        $lama = [$dokumentasi->foto_path, $dokumentasi->thumbnail_path];

        Storage::disk('public')->put($dasar.'.jpg', $this->perkecil($gambar, self::SISI_BESAR, 82));
        Storage::disk('public')->put($dasar.'-kecil.jpg', $this->perkecil($gambar, self::SISI_THUMBNAIL, 78));

        $dokumentasi->forceFill([
            'foto_path' => $dasar.'.jpg',
            'thumbnail_path' => $dasar.'-kecil.jpg',
        ])->save();

        Storage::disk('public')->delete(array_filter($lama));
    }

    public function hapus(Dokumentasi $dokumentasi): void
    {
        Storage::disk('public')->delete(array_filter([$dokumentasi->foto_path, $dokumentasi->thumbnail_path]));
    }

    /**
     * Perkecil (tidak pernah memperbesar) dengan sisi terpanjang $sisi, hasil JPG.
     */
    private function perkecil(\GdImage $gambar, int $sisi, int $kualitas): string
    {
        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);
        $skala = min(1, $sisi / max($lebar, $tinggi));
        $lebarBaru = max(1, (int) round($lebar * $skala));
        $tinggiBaru = max(1, (int) round($tinggi * $skala));

        $hasil = imagecreatetruecolor($lebarBaru, $tinggiBaru);
        // Latar putih untuk PNG transparan (JPG tidak punya transparansi).
        imagefill($hasil, 0, 0, (int) imagecolorallocate($hasil, 255, 255, 255));
        imagecopyresampled($hasil, $gambar, 0, 0, 0, 0, $lebarBaru, $tinggiBaru, $lebar, $tinggi);

        ob_start();
        imagejpeg($hasil, null, $kualitas);

        return (string) ob_get_clean();
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
}
