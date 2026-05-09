<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\LoginData;
use App\DTOs\RegisterData;
use App\Models\User;

readonly class AuthService
{
    public function register(RegisterData $data): User
    {
        // The User model automatically hashes passwords due to the 'hashed' cast
        return User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);
    }

    public function login(LoginData $data): ?string
    {
        if (! auth()->attempt(['email' => $data->email, 'password' => $data->password])) {
            return null;
        }

        /** @var User $user */
        $user = auth()->user();

        // Return a new Sanctum token
        return $user->createToken('auth_token')->plainTextToken;
    }
}
