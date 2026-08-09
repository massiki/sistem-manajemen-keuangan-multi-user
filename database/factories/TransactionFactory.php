<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'expense']);

        return [
            'user_id' => User::factory(),
            'account_id' => Account::factory(),
            'category_id' => Category::factory(),
            'transaction_date' => fake()->date(),
            'type' => $type,
            'amount' => fake()->numberBetween(1_000, 10_000_000),
            'description' => fake()->sentence(),
            'notes' => fake()->sentence(),
        ];
    }
}
