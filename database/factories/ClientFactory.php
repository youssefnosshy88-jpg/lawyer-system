<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'type' => 'individual',
            'name' => fake()->name(),
            'national_id' => fake()->numerify('##############'),
            'phone' => fake()->numerify('01#########'),
            'email' => fake()->unique()->safeEmail(),
            'city' => 'القاهرة',
            'is_active' => true,
        ];
    }

    public function company(): static
    {
        return $this->state(fn () => [
            'type' => 'company',
            'name' => fake()->company(),
            'commercial_register_no' => fake()->numerify('######'),
            'national_id' => null,
        ]);
    }
}
