<?php

use App\Models\Book;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('allows a member to list books', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'member']));

    $this->getJson('/api/v1/books')->assertStatus(200);
});

it('prevents a member from creating a book with 403', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'member']));

    $this->postJson('/api/v1/books', [
        'title'  => 'Test Book',
        'author' => 'Author',
        'isbn'   => '978-3-000000-00-0',
    ])->assertStatus(403);
});

it('allows a librarian to create a book', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'librarian']));

    $this->postJson('/api/v1/books', [
        'title'  => 'New Book',
        'author' => 'Author Name',
        'isbn'   => '978-3-000000-01-7',
    ])->assertStatus(201);
});

it('allows a librarian to delete a book', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'librarian']));
    $book = Book::factory()->create();

    $this->deleteJson("/api/v1/books/{$book->id}")->assertStatus(200);
});

it('rejects book creation with missing isbn with 422', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'librarian']));

    $this->postJson('/api/v1/books', [
        'title'  => 'No ISBN Book',
        'author' => 'Author',
    ])->assertStatus(422);
});
