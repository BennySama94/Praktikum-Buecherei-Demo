<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;

Route::prefix('v1')->group(function () {

    // Auth (public)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login',    [AuthController::class, 'login']);

    // Auth (protected)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me',      [AuthController::class, 'me']);

        // Books
        Route::get('/books',          [BookController::class, 'index']);
        Route::post('/books',         [BookController::class, 'store']);
        Route::get('/books/{book}',   [BookController::class, 'show']);
        Route::put('/books/{book}',   [BookController::class, 'update']);
        Route::delete('/books/{book}',[BookController::class, 'destroy']);

        // Loans
        Route::get('/loans',                    [LoanController::class, 'index']);
        Route::post('/loans',                   [LoanController::class, 'store']);
        Route::get('/loans/{loan}',             [LoanController::class, 'show']);
        Route::patch('/loans/{loan}/return',    [LoanController::class, 'return']);
    });

});
