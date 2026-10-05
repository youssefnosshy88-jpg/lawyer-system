<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory()->company(),
            'name' => fake()->company(),
            'legal_form' => 'llc',
            'status' => 'active',
            'issued_capital' => 100000,
            'paid_capital' => 100000,
            'commercial_register_no' => fake()->numerify('######'),
            'incorporated_at' => now()->subYears(2),
        ];
    }
}
