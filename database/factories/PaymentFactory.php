<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'amount' => fake()->numberBetween(500, 20000),
            'method' => 'cash',
            'paid_at' => now(),
        ];
    }
}
