<?php

namespace Database\Factories;

use App\Models\Dokumentasi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dokumentasi>
 */
class DokumentasiFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nama = fake()->unique()->numerify('kegiatan-####');

        return [
            'judul' => rtrim(fake()->sentence(4), '.'),
            'keterangan' => fake()->sentence(12),
            'tanggal_kegiatan' => fake()->dateTimeBetween('-1 year')->format('Y-m-d'),
            'foto_path' => Dokumentasi::FOLDER.'/'.$nama.'.jpg',
            'thumbnail_path' => Dokumentasi::FOLDER.'/'.$nama.'-kecil.jpg',
            'urutan' => Dokumentasi::urutanBerikutnya(),
            'tampil' => true,
        ];
    }

    public function tersembunyi(): static
    {
        return $this->state(fn () => ['tampil' => false]);
    }
}
