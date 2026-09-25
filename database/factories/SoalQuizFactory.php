<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoalQuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'pertanyaan' => $this->faker->sentence(),
            'tipe' => 'pilihan_ganda',
            'opsi' => ['A', 'B', 'C', 'D', 'E'],
            'kunci_jawaban' => ['A'],
            'skor_maks' => 10,
            'poin_dasar' => 50,
            'level_bloom' => 'C3',
            'urutan' => 1,
            'is_aktif' => true,
        ];
    }
}
