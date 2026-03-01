<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'email'    => 'librarian@demo.test',
            'password' => bcrypt('testpass'),
            'role'     => 'librarian',
        ]);

        $members = User::factory()->count(5)->create([
            'password' => bcrypt('testpass'),
            'role'     => 'member',
        ]);

        $books = Book::factory()->count(20)->create();

        // 5 active loans — one per member
        $members->each(fn ($member) => Loan::factory()->create([
            'user_id' => $member->id,
            'book_id' => $books->random()->id,
        ]));

        // 2 returned
        Loan::factory()->count(2)->returned()->create([
            'user_id' => $members->random()->id,
            'book_id' => $books->random()->id,
        ]);

        // 1 overdue
        Loan::factory()->overdue()->create([
            'user_id' => $members->random()->id,
            'book_id' => $books->random()->id,
        ]);
    }
}
