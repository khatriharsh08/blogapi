<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register',[AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(
    function () {
        Route::post('/posts',[PostController::class, 'store']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    }
);

Route::get('/posts',[PostController::class, 'index']);
Route::get('/posts/{post}',[PostController::class, 'show']);