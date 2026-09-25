<?php

namespace Database\Factories;

use App\Models\Materi;
use Illuminate\Database\Eloquent\Factories\Factory;

class PraktikumFactory extends Factory
{
    public function definition(): array
    {
        return [
            'materi_id'   => Materi::factory(),
            'judul'       => $this->faker->sentence(3),
            'studi_kasus' => $this->faker->paragraph(5),
            'is_aktif'    => true,
        ];
    }
}
