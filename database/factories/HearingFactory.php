<?php

namespace Database\Factories;

use App\Models\Hearing;
use App\Models\LegalCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class HearingFactory extends Factory
{
    protected $model = Hearing::class;

    public function definition(): array
    {
        return [
            'legal_case_id' => LegalCase::factory(),
            'scheduled_at' => now()->addDays(fake()->numberBetween(1, 30))->setTime(10, 0),
            'type' => 'pleading',
            'status' => 'scheduled',
        ];
    }
}
