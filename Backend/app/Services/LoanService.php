<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Carbon;

class LoanService
{
    public function checkout(User $user, Book $book): Loan
    {
        $alreadyLoaned = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'active')
            ->exists();

        abort_if($alreadyLoaned, 422, 'Du hast das Buch bereits ausgeliehen.');

        return Loan::create([
            'user_id'  => $user->id,
            'book_id'  => $book->id,
            'status'   => 'active',
            'due_date' => Carbon::now()->addDays(14),
        ]);
    }

    public function returnBook(Loan $loan): Loan
    {
        $loan->update([
            'status'      => 'returned',
            'returned_at' => Carbon::now(),
        ]);

        return $loan->fresh();
    }

    public function markOverdue(): int
    {
        return Loan::where('status', 'active')
            ->where('due_date', '<', Carbon::now())
            ->update(['status' => 'overdue']);
    }
}
