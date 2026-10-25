<?php

namespace App\Actions\Auth;

use App\Enuns\UserRole;
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

        $expiresAt = match ($user->role) {
            UserRole::admin => now()->addHours(8),    
            UserRole::operator => now()->addHours(12),   
            UserRole::manager => now()->addHours(12),   
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