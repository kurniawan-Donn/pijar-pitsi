<?php

namespace Database\Factories;

use App\Models\PesertaMagang;
use App\Models\ScanKartuAbsensi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScanKartuAbsensi>
 */
class ScanKartuAbsensiFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'peserta_magang_id' => fn () => PesertaMagang::create(['nama' => fake()->name(), 'status' => 'aktif'])->id,
            'tahun' => 2026,
            'bulan' => 10,
            'sisi' => ScanKartuAbsensi::SISI_AWAL,
            'foto_path' => ScanKartuAbsensi::FOLDER.'/'.fake()->uuid().'.jpg',
            'status' => ScanKartuAbsensi::DIPRATINJAU,
            'hasil' => [],
            'model' => 'gemini-2.5-flash',
        ];
    }
}
