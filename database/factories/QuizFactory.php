<?php

namespace Database\Factories;

use App\Models\Materi;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            'materi_id'            => Materi::factory(),
            'judul'                => $this->faker->sentence(4),
            'durasi_menit'         => $this->faker->randomElement([20, 30, 45, 60]),
            'bonus_kecepatan_maks' => 50,
            'is_aktif'             => true,
        ];
    }
}
