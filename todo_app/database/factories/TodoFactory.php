<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    public function definition(): array
    {
        $startAt = fake()->dateTimeBetween('+1 day', '+2 weeks');

        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement(['請求書をまとめる', '牛乳を買う', 'Laravel を復習する']).' '.fake()->unique()->numberBetween(1, 100000),
            'memo' => '忘れないうちに片づけておく。',
            'category' => fake()->randomElement(['仕事', '買い物', '勉強']),
            'is_done' => false,
            'start_at' => $startAt,
            'due_at' => (clone $startAt)->modify('+3 days'),
        ];
    }

    public function done(): static
    {
        return $this->state(['is_done' => true]);
    }
}
