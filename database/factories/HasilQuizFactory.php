<?php

namespace Database\Factories;

use App\Models\Murid;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class HasilQuizFactory extends Factory
{
    public function definition(): array
    {
        $mulai = now()->subMinutes($this->faker->numberBetween(10, 60));

        return [
            'quiz_id'       => Quiz::factory(),
            'murid_id'      => Murid::factory(),
            'waktu_mulai'   => $mulai,
            'waktu_selesai' => $mulai->addMinutes($this->faker->numberBetween(10, 30)),
            'skor_total'    => $this->faker->numberBetween(0, 100),
            'poin_total'    => $this->faker->numberBetween(0, 150),
        ];
    }
}
