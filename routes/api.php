<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login')->name('login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::controller(PostController::class)->group(function () {
        Route::post('/posts', 'store');
        Route::put('/posts/{post}', 'update');
        Route::delete('/posts/{post}', 'destroy');
    });

    Route::controller(CommentController::class)->group(function () {
        Route::post('/posts/{post}/comments', 'store');
        Route::delete('/comments/{comment}', 'destroy');
    });
});

Route::controller(PostController::class)->group(function () {
    Route::get('/posts', 'index');
    Route::get('/posts/{post}', 'show');
});

Route::get('/posts/{post}/comments', [CommentController::class, 'index']);