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
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $token = $this->authService->login($request->only('email', 'password'));
        if (!$token) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => auth()->user()
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
