<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->words(2, true),
            'type' => 'cash',
            'initial_balance' => fake()->numberBetween(0, 10_000_000),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
