<?php

declare(strict_types=1);

namespace App\Shared\Domain\Interfaces;

interface TransactionManagerInterface
{
    public function run(callable $callback): mixed;
}