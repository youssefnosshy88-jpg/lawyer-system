<?php

namespace Database\Factories;

use App\Models\CaseType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CaseTypeFactory extends Factory
{
    protected $model = CaseType::class;

    public function definition(): array
    {
        return ['name' => fake()->word(), 'is_active' => true];
    }
}
