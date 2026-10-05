<?php

namespace Database\Factories;

use App\Models\Court;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourtFactory extends Factory
{
    protected $model = Court::class;

    public function definition(): array
    {
        return ['name' => 'محكمة '.fake()->city(), 'city' => fake()->city(), 'is_active' => true];
    }
}
