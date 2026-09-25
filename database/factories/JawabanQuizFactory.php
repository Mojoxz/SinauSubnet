<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\HasilQuiz;
use App\Models\SoalQuiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class JawabanQuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hasil_quiz_id' => HasilQuiz::factory(),
            'soal_quiz_id' => SoalQuiz::factory(),
            'jawaban' => $this->faker->word(),
            'skor_ai' => 0,
            'skor_final' => 0,
            'status_penilaian' => 'menunggu_ai',
        ];
    }
}
