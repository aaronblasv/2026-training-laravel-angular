<?php

declare(strict_types=1);

namespace App\User\Domain\Interfaces;

use App\User\Domain\ValueObject\Pin;

interface PinGeneratorInterface
{
    public function generate(): Pin;
}