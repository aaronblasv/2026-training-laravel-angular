<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Services;

use App\User\Domain\Entity\User;
use App\User\Domain\Interfaces\TokenGeneratorInterface;
use App\User\Infrastructure\Persistence\Models\EloquentUser;

class LaravelTokenGenerator implements TokenGeneratorInterface
{
    public function generateToken(User $user): string
    {
        $eloquentUser = EloquentUser::where('uuid', $user->uuid()->getValue())->firstOrFail();
        return $eloquentUser->createToken('auth-token')->plainTextToken;
    }

    public function revokeTokens(User $user): void
    {
        $eloquentUser = EloquentUser::where('uuid', $user->uuid()->getValue())->first();

        if ($eloquentUser === null) {
            return;
        }

        $eloquentUser->tokens()->delete();
    }

    public function revokeTokensByUuid(string $uuid): void
    {
        $eloquentUser = EloquentUser::where('uuid', $uuid)->first();

        if ($eloquentUser === null) {
            return;
        }

        $eloquentUser->tokens()->delete();
    }
}