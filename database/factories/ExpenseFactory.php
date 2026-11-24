<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $admin = User::first();

        return [
            'category' => fake()->randomElement(Expense::CATEGORIES),
            'amount' => fake()->randomElement([50000, 100000, 250000, 420000, 1100000]),
            'note' => fake()->sentence(3),
            'fund_source' => fake()->randomElement(Expense::FUND_SOURCES),
            'occurred_at' => now(),
            'attachment_url' => null,
            'created_by' => $admin?->id_user ?? 1,
        ];
    }
}
