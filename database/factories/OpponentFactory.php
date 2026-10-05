<?php

namespace Database\Factories;

use App\Models\Opponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpponentFactory extends Factory
{
    protected $model = Opponent::class;

    public function definition(): array
    {
        return [
            'type' => 'individual',
            'name' => fake()->name(),
        ];
    }
}
