<?php

namespace App\Services;

use App\Models\PengaturanMagang;
use App\Models\SuratKeterangan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Gambar tanda tangan (Mengetahui, Menyetujui) dan stempel untuk PDF penilaian
 * dan surat keterangan. Disimpan di disk privat sebagai PNG (transparansi
 * dipertahankan) dan disisipkan ke PDF sebagai data URI.
 *
 * Surat terbit menyimpan path gambar di salinan datanya, jadi file lama tidak
 * dihapus selama masih dipakai surat terbit.
 */
class TandaTanganDokumen
{
    public const FOLDER = 'tanda-tangan';

    /**
     * Jenis gambar → kolom di pengaturan_magangs.
     *
     * @var array<string, string>
     */
    public const KOLOM = [
        'ttd_mengetahui' => 'mengetahui_ttd_path',
        'ttd_menyetujui' => 'menyetujui_ttd_path',
        'stempel' => 'stempel_path',
    ];

    /**
     * Label tiap jenis untuk tampilan dan log.
     *
     * @var array<string, string>
     */
    public const LABEL = [
        'ttd_mengetahui' => 'Tanda tangan Mengetahui',
        'ttd_menyetujui' => 'Tanda tangan Menyetujui',
        'stempel' => 'Stempel',
    ];

    /**
     * Ukuran maksimal hasil olahan (lebar × tinggi piksel).
     *
     * @var array<string, array{int, int}>
     */
    private const UKURAN_MAKS = [
        'ttd_mengetahui' => [600, 300],
        'ttd_menyetujui' => [600, 300],
        'stempel' => [400, 400],
    ];

    /**
     * Aturan validasi file unggahan.
     *
     * @return array<int, string>
     */
    public static function aturan(): array
    {
        return ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:1024', 'dimensions:min_width=80,min_height=40,max_width=4000,max_height=4000'];
    }

    /**
     * Simpan gambar baru untuk satu jenis, ganti yang lama.
     */
    public function simpan(PengaturanMagang $pengaturan, string $jenis, UploadedFile $file): void
    {
        $path = self::FOLDER.'/'.$jenis.'-'.Str::random(16).'.png';
        Storage::disk('local')->put($path, $this->olah($file, $jenis));

        $this->ganti($pengaturan, $jenis, $path);
    }

    public function hapus(PengaturanMagang $pengaturan, string $jenis): void
    {
        $this->ganti($pengaturan, $jenis, null);
    }

    /**
     * Isi gambar sebagai data URI PNG untuk dompdf, atau null bila tidak ada.
     */
    public function dataUri(?string $path): ?string
    {
        if (! $path || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode((string) Storage::disk('local')->get($path));
    }

    private function ganti(PengaturanMagang $pengaturan, string $jenis, ?string $pathBaru): void
    {
        $kolom = self::KOLOM[$jenis];
        $lama = $pengaturan->{$kolom};

        $pengaturan->forceFill([$kolom => $pathBaru])->save();

        if ($lama && ! $this->dipakaiSuratTerbit($lama)) {
            Storage::disk('local')->delete($lama);
        }
    }

    private function dipakaiSuratTerbit(string $path): bool
    {
        return SuratKeterangan::query()
            ->where('data->penanda_tangan->ttd_path', $path)
            ->orWhere('data->penanda_tangan->stempel_path', $path)
            ->exists();
    }

    /**
     * Perkecil bila terlalu besar dan simpan ulang sebagai PNG (sekaligus membuang metadata).
     */
    private function olah(UploadedFile $file, string $jenis): string
    {
        $gambar = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($gambar === false) {
            throw ValidationException::withMessages([$jenis => 'File gambar tidak dapat dibaca. Coba file lain.']);
        }

        [$maksLebar, $maksTinggi] = self::UKURAN_MAKS[$jenis];
        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);
        $skala = min(1, $maksLebar / $lebar, $maksTinggi / $tinggi);
        $lebarBaru = max(1, (int) round($lebar * $skala));
        $tinggiBaru = max(1, (int) round($tinggi * $skala));

        $hasil = imagecreatetruecolor($lebarBaru, $tinggiBaru);
        imagealphablending($hasil, false);
        imagesavealpha($hasil, true);
        imagefill($hasil, 0, 0, (int) imagecolorallocatealpha($hasil, 255, 255, 255, 127));
        imagecopyresampled($hasil, $gambar, 0, 0, 0, 0, $lebarBaru, $tinggiBaru, $lebar, $tinggi);

        ob_start();
        imagepng($hasil, null, 9);

        return (string) ob_get_clean();
    }
}
