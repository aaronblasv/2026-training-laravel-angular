<?php

declare(strict_types=1);

namespace App\Shared\Domain;

interface CacheRepositoryInterface
{
    public function remember(string $key, int $ttlSeconds, callable $callback): mixed;
    public function forget(string $key): void;
    public function forgetByPrefix(string $prefix): void;
}