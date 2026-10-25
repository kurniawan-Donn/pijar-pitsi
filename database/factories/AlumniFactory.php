<?php

namespace Database\Factories;

use App\Models\Alumni;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Alumni manual (tanpa peserta), tampil di website.
 *
 * @extends Factory<Alumni>
 */
class AlumniFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'peserta_magang_id' => null,
            'nama' => fake()->name(),
            'universitas' => 'Universitas '.fake()->city(),
            'program_studi' => 'Akuntansi',
            'divisi' => 'Unit Umum',
            'periode' => 'Januari – Juni 2025',
            'rating' => fake()->numberBetween(3, 5),
            'judul_project' => 'Digitalisasi arsip audit',
            'deskripsi_project' => fake()->paragraph(),
            'testimoni' => fake()->sentence(),
            'urutan' => Alumni::urutanBerikutnya(),
            'tampil' => true,
        ];
    }

    public function tersembunyi(): static
    {
        return $this->state(fn () => ['tampil' => false]);
    }
}
