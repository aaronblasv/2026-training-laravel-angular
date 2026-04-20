<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Services;

use App\User\Domain\Interfaces\PinGeneratorInterface;
use App\User\Domain\ValueObject\Pin;

class RandomPinGenerator implements PinGeneratorInterface
{
    public function generate(): Pin
    {
        return Pin::create(str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT));
    }
}