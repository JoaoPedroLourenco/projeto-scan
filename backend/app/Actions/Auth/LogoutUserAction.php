<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class  LogoutUserAction{
    public function execute(User $user): bool {
        return DB::transaction(function() use ($user) {
           $token = $user->currentAccessToken();
            
            if (!$token) {
                return false;
            }

            return (bool) $user->tokens()->where('id', $token->id)->delete();
        });
    }
}