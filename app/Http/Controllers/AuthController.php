<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Providers\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

readonly class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 'User registered successfully', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->only('email', 'password'));
        if (!$token) {
            return $this->error('Invalid credentials', 401);
        }
        
        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => auth()->user()
        ], 'Logged in successfully', 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logged out successfully', 200);
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success($request->user(), 'User fetched successfully');
    }
}
