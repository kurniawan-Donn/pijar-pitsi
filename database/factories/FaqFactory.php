<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pertanyaan' => rtrim(fake()->sentence(6), '.').'?',
            'jawaban' => fake()->paragraph(),
            'kategori' => null,
            'urutan' => Faq::urutanBerikutnya(),
            'tampil' => true,
        ];
    }

    public function tersembunyi(): static
    {
        return $this->state(fn () => ['tampil' => false]);
    }
}
