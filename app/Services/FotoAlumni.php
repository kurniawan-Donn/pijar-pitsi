<?php

namespace App\Services;

use App\Models\Alumni;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Foto alumni di disk "public" (bisa dibuka siapa saja).
 *
 * - Alumni dari peserta: foto profil peserta (disk privat) disalin ke publik
 *   hanya selama alumni tampil di website; disembunyikan atau persetujuan
 *   ditarik → salinan dihapus.
 * - Alumni manual: foto diunggah admin.
 */
class FotoAlumni
{
    public function __construct(private FotoProfil $fotoProfil) {}

    /**
     * Samakan foto publik alumni dari peserta dengan status tampilnya.
     */
    public function sinkron(Alumni $alumni): void
    {
        if (! $alumni->dariPeserta()) {
            return;
        }

        $alumni->loadMissing('pesertaMagang.user', 'pesertaMagang.feedback');
        $sumber = $alumni->pesertaMagang?->user?->foto_path;

        if (! $alumni->tampilDiWebsite() || ! $sumber || ! Storage::disk('local')->exists($sumber)) {
            $this->hapus($alumni);

            return;
        }

        $this->ganti($alumni, (string) Storage::disk('local')->get($sumber));
    }

    public function simpanUnggahan(Alumni $alumni, UploadedFile $file): void
    {
        $this->ganti($alumni, $this->fotoProfil->olah($file));
    }

    public function hapus(Alumni $alumni): void
    {
        if ($alumni->foto_path) {
            Storage::disk('public')->delete($alumni->foto_path);
            $alumni->forceFill(['foto_path' => null])->save();
        }
    }

    private function ganti(Alumni $alumni, string $isi): void
    {
        $lama = $alumni->foto_path;
        $path = Alumni::FOLDER_FOTO.'/'.$alumni->id.'-'.Str::random(12).'.jpg';

        Storage::disk('public')->put($path, $isi);
        $alumni->forceFill(['foto_path' => $path])->save();

        if ($lama) {
            Storage::disk('public')->delete($lama);
        }
    }
}
