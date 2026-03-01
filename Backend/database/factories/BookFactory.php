<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'        => fake()->words(3, true),
            'author'       => fake()->name(),
            'isbn'         => fake()->unique()->isbn13(),
            'genre'        => fake()->randomElement(['Fiction', 'Non-Fiction', 'Science', 'History', 'Biography']),
            'year'         => fake()->numberBetween(1950, 2024),
            'total_copies' => fake()->numberBetween(1, 5),
        ];
    }
}
