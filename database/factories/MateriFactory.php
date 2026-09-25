<?php

namespace Database\Factories;

use App\Models\Guru;
use Illuminate\Database\Eloquent\Factories\Factory;

class MateriFactory extends Factory
{
    public function definition(): array
    {
        return [
            'guru_id' => Guru::factory(),
            'level'   => $this->faker->numberBetween(1, 4),
            'judul'   => $this->faker->sentence(4),
            'konten'  => $this->faker->paragraphs(3, true),
            'urutan'  => $this->faker->numberBetween(1, 20),
            'is_aktif'=> true,
        ];
    }
}
