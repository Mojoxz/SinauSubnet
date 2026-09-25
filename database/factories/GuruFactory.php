<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuruFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nip'     => $this->faker->optional()->numerify('####################'),
        ];
    }
}
