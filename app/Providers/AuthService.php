<?php

namespace App\Providers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthService
{
    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function login(array $credentials)
    {
        if (!auth()->attempt($credentials)) {
            return null;
        }

        return auth()->user()->createToken('auth_token')->plainTextToken;
    }
}
