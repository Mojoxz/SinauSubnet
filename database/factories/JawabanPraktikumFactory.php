<?php

namespace Database\Factories;

use App\Enums\StatusPenilaian;
use App\Models\Murid;
use App\Models\SoalPraktikum;
use Illuminate\Database\Eloquent\Factories\Factory;

class JawabanPraktikumFactory extends Factory
{
    public function definition(): array
    {
        return [
            'soal_praktikum_id' => SoalPraktikum::factory(),
            'murid_id'          => Murid::factory(),
            'jawaban'           => $this->faker->sentence(3),
            'file_bukti_path'   => null,
            'skor_ai'           => null,
            'skor_final'        => null,
            'status_penilaian'  => StatusPenilaian::FINAL_OTOMATIS->value,
            'sampel_review'     => false,
        ];
    }
}
