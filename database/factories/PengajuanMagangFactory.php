<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use App\Models\PengajuanMagang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengajuanMagang>
 */
class PengajuanMagangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mulai = fake()->dateTimeBetween('+1 week', '+1 month');

        return [
            'mahasiswa_id' => Mahasiswa::factory(),
            'perusahaan_id' => null,
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => (clone $mulai)->modify('+3 months'),
            'posisi' => fake()->randomElement(['Web Developer', 'Administrasi', 'Desain Grafis']),
            'status' => PengajuanMagang::STATUS_MENUNGGU,
            'keterangan' => fake()->sentence(),
        ];
    }

    public function disetujui(): static
    {
        return $this->state(['status' => PengajuanMagang::STATUS_DISETUJUI]);
    }

    public function ditolak(): static
    {
        return $this->state([
            'status' => PengajuanMagang::STATUS_DITOLAK,
            'catatan_admin' => 'Kuota bidang ini sudah penuh.',
        ]);
    }

    public function denganDokumen(string $path = 'pengajuan/surat.pdf'): static
    {
        return $this->state(['dokumen_path' => $path]);
    }
}
