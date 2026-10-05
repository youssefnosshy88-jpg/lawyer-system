<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'status' => 'sent',
            'issued_at' => now(),
            'due_at' => now()->addDays(30),
            'currency' => 'EGP',
        ];
    }
}
