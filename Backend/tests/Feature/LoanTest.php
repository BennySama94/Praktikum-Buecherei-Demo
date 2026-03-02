<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('allows a member to checkout an available book', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'member']));
    $book = Book::factory()->create(['total_copies' => 1]);

    $this->postJson('/api/v1/loans', ['book_id' => $book->id])
         ->assertStatus(201);
});

it('prevents a member from checking out an unavailable book with 422', function () {
    $member = User::factory()->create(['role' => 'member']);
    Sanctum::actingAs($member);

    // All copies already on loan
    $book = Book::factory()->create(['total_copies' => 1]);
    Loan::factory()->create(['book_id' => $book->id, 'status' => 'active']);

    $this->postJson('/api/v1/loans', ['book_id' => $book->id])
         ->assertStatus(422);
});

it('allows a member to return their own loan', function () {
    $member = User::factory()->create(['role' => 'member']);
    Sanctum::actingAs($member);
    $loan = Loan::factory()->create(['user_id' => $member->id, 'status' => 'active']);

    $this->patchJson("/api/v1/loans/{$loan->id}/return")
         ->assertStatus(200);
});

it('prevents a member from viewing another member\'s loan with 403', function () {
    $other = User::factory()->create(['role' => 'member']);
    $loan  = Loan::factory()->create(['user_id' => $other->id]);

    Sanctum::actingAs(User::factory()->create(['role' => 'member']));

    $this->getJson("/api/v1/loans/{$loan->id}")->assertStatus(403);
});

it('allows a librarian to view all loans', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'librarian']));
    Loan::factory()->count(3)->create();

    $this->getJson('/api/v1/loans')->assertStatus(200)->assertJsonCount(3, 'data');
});

it('marks past-due active loans as overdue via markOverdue()', function () {
    $loan = Loan::factory()->create([
        'status'   => 'active',
        'due_date' => now()->subDay(),
    ]);

    $count = app(\App\Services\LoanService::class)->markOverdue();

    expect($count)->toBe(1);
    expect($loan->fresh()->status)->toBe('overdue');
});


it('allows a member to view their own loans', function () {
    $member = User::factory()->create(['role' => 'member']);
    Sanctum::actingAs($member);
    Loan::factory()->count(2)->create(['user_id' => $member->id]);
    Loan::factory()->create(); // someone else's — should not appear

    $this->getJson('/api/v1/loans')
         ->assertStatus(200)
         ->assertJsonCount(2, 'data');
});

it('prevents a member from borrowing the same book twice', function () {
    $member = User::factory()->create(['role' => 'member']);
    Sanctum::actingAs($member);
    $book = Book::factory()->create(['total_copies' => 3]);

    // First loan — should succeed
    Loan::factory()->create([
        'user_id' => $member->id,
        'book_id' => $book->id,
        'status'  => 'active',
    ]);

    // Second attempt — same book, same user
    $this->postJson('/api/v1/loans', ['book_id' => $book->id])
         ->assertStatus(422);
});