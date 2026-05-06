<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

readonly class AuthService
{
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function login(array $credentials): ?string
    {
        if (!auth()->attempt($credentials)) {
            return null;
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        return $user->createToken('auth_token')->plainTextToken;
    }
}
