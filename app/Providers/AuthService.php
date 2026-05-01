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

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null;
        }
        return $user->createToken('api-token')->plainTextToken;
    }
}
