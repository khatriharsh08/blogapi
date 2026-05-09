<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login')->name('login')->middleware('throttle:5,1');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::controller(PostController::class)->group(function () {
            Route::post('/posts', 'store')->middleware('throttle:api');
            Route::put('/posts/{post}', 'update');
            Route::delete('/posts/{post}', 'destroy');
        });

        Route::controller(CommentController::class)->group(function () {
            Route::post('/posts/{post}/comments', 'store')->middleware('throttle:api');
            Route::delete('/comments/{comment}', 'destroy');
        });
    });

    Route::controller(PostController::class)->group(function () {
        Route::get('/posts', 'index');
        Route::get('/posts/{post}', 'show');
    });

    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
});
