<?php

namespace Database\Factories;

use App\Enums\LevelBloom;
use App\Enums\TipeSoal;
use App\Models\Praktikum;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoalPraktikumFactory extends Factory
{
    public function definition(): array
    {
        return [
            'praktikum_id'  => Praktikum::factory(),
            'pertanyaan'    => $this->faker->sentence(8) . '?',
            'tipe'          => TipeSoal::ISIAN->value,
            'level_bloom'   => $this->faker->randomElement([LevelBloom::C3->value, LevelBloom::C4->value, LevelBloom::C5->value]),
            'skor_maks'     => 10,
            'kunci_jawaban' => ['192.168.1.0', '/26'],
            'rubrik'        => null,
            'pembahasan'    => $this->faker->sentence(),
            'hint'          => ['Perhatikan oktet ke-3', 'Hitung jumlah bit host'],
            'urutan'        => $this->faker->numberBetween(1, 10),
            'is_aktif'      => true,
        ];
    }
}
