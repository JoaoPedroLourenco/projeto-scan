<?php

namespace App\Actions\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginUserAction
{
    public function execute(array $data): ?array
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => true,
        ];

        if ( !Auth::attempt($credentials) ) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user();

        $abilities = ['*'];

        $expiresAt = $user->role->expiresAt();

        $rawTokenName = $data['device_name'] ?? request()->userAgent() ?? 'api-token';
        $tokenName = Str::limit($rawTokenName, 255, '');

        $token = $user->createToken($tokenName, $abilities, $expiresAt)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}