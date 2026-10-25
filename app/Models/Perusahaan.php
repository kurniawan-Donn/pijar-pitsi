<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perusahaan extends Model
{
    protected $table = 'perusahaans';

    protected $fillable = [
        'nama_perusahaan',
        'nama',
        'alamat',
        'kota',
        'no_telepon',
        'telepon',
        'email',
        'website',
        'nama_pembimbing',
        'jabatan_pembimbing',
        'deskripsi',
        'tampilkan_peta',
        'peta_embed_url',
    ];

    protected function casts(): array
    {
        return [
            'tampilkan_peta' => 'boolean',
        ];
    }

    public function pengajuanMagangs(): HasMany
    {
        return $this->hasMany(PengajuanMagang::class);
    }

    /**
     * Alamat lengkap untuk pencarian peta (alamat + kota bila belum tercantum).
     */
    public function alamatLengkap(): ?string
    {
        $alamat = trim((string) $this->alamat);
        $kota = trim((string) $this->kota);

        if ($alamat === '') {
            return null;
        }

        return $kota !== '' && ! str_contains(mb_strtolower($alamat), mb_strtolower($kota))
            ? $alamat.', '.$kota
            : $alamat;
    }

    /**
     * URL sematan peta untuk iframe: tautan sematan dari admin, atau dibuat dari alamat.
     * Null bila peta disembunyikan atau tidak ada alamat.
     */
    public function urlPeta(): ?string
    {
        if (! $this->tampilkan_peta) {
            return null;
        }

        if ($this->peta_embed_url) {
            return $this->peta_embed_url;
        }

        $alamat = $this->alamatLengkap();

        return $alamat ? 'https://maps.google.com/maps?q='.rawurlencode($alamat).'&output=embed' : null;
    }

    /**
     * Tautan "Buka di Google Maps" (aplikasi peta di HP / tab baru di komputer).
     */
    public function urlBukaPeta(): ?string
    {
        $alamat = $this->alamatLengkap();

        return $alamat ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($alamat) : null;
    }

    /**
     * Ambil URL sematan Google Maps dari isian admin: boleh kode <iframe> lengkap atau
     * URL-nya saja. Hanya alamat sematan resmi Google Maps yang diterima (keamanan iframe).
     *
     * @return string|false|null URL sematan, null bila kosong, false bila bukan sematan Google Maps
     */
    public static function normalisasiPetaEmbed(?string $isian): string|false|null
    {
        $isian = trim((string) $isian);

        if ($isian === '') {
            return null;
        }

        if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $isian, $cocok)) {
            $isian = $cocok[1];
        }

        $isian = html_entity_decode($isian, ENT_QUOTES | ENT_HTML5);

        return preg_match('#^https://(www\.)?google\.[a-z.]+/maps/embed\?[^\s"\'<>]+$#i', $isian)
            || preg_match('#^https://maps\.google\.[a-z.]+/maps\?[^\s"\'<>]*output=embed[^\s"\'<>]*$#i', $isian)
            ? $isian
            : false;
    }
}
