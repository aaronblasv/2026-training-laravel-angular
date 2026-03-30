<?php

namespace App\User\Infrastructure\Persistence\Models;

class SanctumTokenGenerator
{
    public function __invoke(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }
}