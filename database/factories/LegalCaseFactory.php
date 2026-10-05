<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\LegalCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegalCaseFactory extends Factory
{
    protected $model = LegalCase::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'case_number' => (string) fake()->numberBetween(1, 9999),
            'case_year' => now()->year,
            'client_id' => Client::factory(),
            'client_role' => 'plaintiff',
            'degree' => 'first_instance',
            'status' => 'open',
            'filed_at' => now()->subDays(fake()->numberBetween(1, 90)),
        ];
    }
}
