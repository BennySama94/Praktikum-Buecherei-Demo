<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $book = Book::withAvailability()->get();
        return BookResource::collection($book);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = Book::create($request->validated());

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): BookResource
    {
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $this->authorize('update', $book);

        $book->update($request->validated());

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): JsonResponse
    {
        $this->authorize('delete', $book);

        $book->delete();

        return response()->json(['message' => 'Buch wurde aus der Datenbank gelöscht.']);
    }
}
