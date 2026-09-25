<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MuridFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'kelas'            => $this->faker->randomElement(['X TKJ 1', 'X TKJ 2', 'XI TKJ 1', 'XI TKJ 2', 'XII TKJ 1']),
            'total_poin'       => 0,
            'poin_dicapai_pada'=> null,
        ];
    }
}
