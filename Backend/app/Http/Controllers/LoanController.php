<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Http\Resources\LoanResource;
use App\Models\Book;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loanService) {}

    public function index(): AnonymousResourceCollection
    {
        $user = auth()->user();

        $loans = $user->role === 'librarian'
            ? Loan::with(['user', 'book'])->get()
            : Loan::with(['user', 'book'])->where('user_id', $user->id)->get();

        return LoanResource::collection($loans);
    }


    public function show(Loan $loan): LoanResource
    {
        $this->authorize('view', $loan);

        return new LoanResource($loan->load(['user', 'book']));
    }

    public function store(StoreLoanRequest $request): JsonResponse
    {
        $book = Book::findOrFail($request->validated('book_id'));

        abort_if($book->available_copies <= 0, 422, 'Keine Exemplare verfügbar.');

        $loan = $this->loanService->checkout($request->user(), $book);

        return (new LoanResource($loan->load(['user', 'book'])))
            ->response()
            ->setStatusCode(201);
    }

    public function return(Loan $loan): LoanResource
    {
        $this->authorize('return', $loan);

        abort_if($loan->status !== 'active', 422, 'Leihgang ist bereits zurückgegeben.');

        $loan = $this->loanService->returnBook($loan);

        return new LoanResource($loan->load(['user', 'book']));
    }
}
