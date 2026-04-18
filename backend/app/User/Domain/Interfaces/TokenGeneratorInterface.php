<?php

declare(strict_types=1);

namespace App\User\Domain\Interfaces;

use App\User\Domain\Entity\User;

interface TokenGeneratorInterface
{
    public function generateToken(User $user): string;
    public function revokeTokens(User $user): void;
    public function revokeTokensByUuid(string $uuid): void;
}