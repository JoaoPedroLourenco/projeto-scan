<?php

namespace App\Actions\Auth;

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
        ];

        if ( !Auth::attempt($credentials) ) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user();

        $abilities = ['*'];
        $expiresAt = match ($user->role) {
            'admin' => now()->addHours(8),    
            'operador' => now()->addHours(12),   
            default => now()->addDays(14),       
        };

        $rawTokenName = $data['device_name'] ?? request()->userAgent() ?? 'api-token';
        $tokenName = Str::limit($rawTokenName, 255, '');

        $token = $user->createToken($tokenName, $abilities, $expiresAt)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}