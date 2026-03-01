<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'book_id'     => Book::factory(),
            'status'      => 'active',
            'due_date'    => now()->addDays(14),
            'returned_at' => null,
        ];
    }

    public function returned(): static
    {
        return $this->state(fn() => [
            'status'      => 'returned',
            'returned_at' => now(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn() => [
            'status'   => 'active',
            'due_date' => now()->subDays(3),
        ]);
    }
}
