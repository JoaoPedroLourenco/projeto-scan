<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserProfileAction
{
    public function execute(User $user, array $data): User
    {
        return DB::transaction(function() use ($user, $data) {
            $user->update($data);

            return $user->fresh();
        });
    }
}