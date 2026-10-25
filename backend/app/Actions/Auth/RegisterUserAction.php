<?php

namespace App\Actions\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterUserAction
{
    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'cpf' => $data['cpf'] ?? null,
                'phone' => $data['phone'] ?? null,
                'role' => UserRole::customer,
                'password' => $data['password'],
            ]);

            $abilities = ['*'];

            $expiresAt = $user->role->expiresAt();

            $rawTokenName = $data['device_name'] ?? request()->userAgent() ?? 'api-token';
            $tokenName = Str::limit($rawTokenName, 255, '');

            $token = $user->createToken($tokenName, $abilities, $expiresAt)->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }
}