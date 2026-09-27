<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegisterUserAction
{
    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'cpf' => $data['cpf'] ?? null,
                'phone' => $data['telefone'] ?? null,
                'role' => 'cliente',
                'password' => $data['password'],
            ]);

            $abilities = ['*'];

            $expiresAt = match ($user->role) {
                'admin' => now()->addHours(8),    
                'operador' => now()->addHours(12),   
                default => now()->addDays(14),       
            };

            $token = $user->createToken('api-token', $abilities, $expiresAt)->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }
}