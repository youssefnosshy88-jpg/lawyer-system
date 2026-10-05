<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= 'password',
            'remember_token' => Str::random(10),
            'is_active' => true,
            'locale' => 'ar',
        ];
    }

    public function admin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->syncRoles('admin'));
    }

    public function lawyer(): static
    {
        return $this->afterCreating(fn (User $user) => $user->syncRoles('lawyer'));
    }

    public function secretary(): static
    {
        return $this->afterCreating(fn (User $user) => $user->syncRoles('secretary'));
    }

    public function accountant(): static
    {
        return $this->afterCreating(fn (User $user) => $user->syncRoles('accountant'));
    }
}
