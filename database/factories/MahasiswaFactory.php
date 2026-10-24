<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nim' => fake()->unique()->numerify('22########'),
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'asal_kampus' => 'Universitas '.fake()->city(),
            'no_hp' => fake()->numerify('08##########'),
            'program_studi' => fake()->randomElement(['Sistem Informasi', 'Teknik Informatika', 'Manajemen']),
            'fakultas' => null,
        ];
    }
}
