<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
        // Constructor injection of the AuthService
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());
        return response()->json($user, 201);
    }

    public function login(LoginRequest $request)
    {
        $token = $this->authService->login($request->validated());
        if (!$token) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        return response()->json(['token' => $token], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
