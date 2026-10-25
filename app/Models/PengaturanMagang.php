<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pengaturan program magang (satu baris).
 */
class PengaturanMagang extends Model
{
    protected $table = 'pengaturan_magangs';

    protected $fillable = [
        'batas_isi_mundur_hari',
        'tanggal_awal_periode',
        'kota_surat',
        'mengetahui_nama',
        'mengetahui_jabatan',
        'menyetujui_nama',
        'menyetujui_jabatan',
        'format_nomor_surat',
        'scan_aktif',
        'scan_jendela_1_mulai',
        'scan_jendela_1_selesai',
        'scan_jendela_2_mulai',
        'scan_jendela_2_selesai',
        'scan_batas_harian',
    ];

    protected $casts = [
        'batas_isi_mundur_hari' => 'integer',
        'scan_aktif' => 'boolean',
        'scan_jendela_1_mulai' => 'integer',
        'scan_jendela_1_selesai' => 'integer',
        'scan_jendela_2_mulai' => 'integer',
        'scan_jendela_2_selesai' => 'integer',
        'scan_batas_harian' => 'integer',
        'tanggal_awal_periode' => 'integer',
    ];

    /**
     * Baris pengaturan yang berlaku (dibuat dengan nilai bawaan bila belum ada).
     */
    public static function ambil(): self
    {
        return self::query()->oldest('id')->first() ?? self::create([
            'batas_isi_mundur_hari' => 7,
            'tanggal_awal_periode' => 28,
            'format_nomor_surat' => '{urut}/SKM/{bulan_romawi}/{tahun}',
        ]);
    }
}
