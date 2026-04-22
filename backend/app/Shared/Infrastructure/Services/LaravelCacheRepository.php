<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Services;

use App\Shared\Domain\CacheRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class LaravelCacheRepository implements CacheRepositoryInterface
{
    public function remember(string $key, int $ttlSeconds, callable $callback): mixed
    {
        return Cache::remember($key, $ttlSeconds, $callback);
    }

    public function forget(string $key): void
    {
        Cache::forget($key);
    }

    public function forgetByPrefix(string $prefix): void
    {
        if (config('cache.default') !== 'database') {
            return;
        }

        $prefixValue = (string) config('cache.prefix', '');
        $table = (string) config('cache.stores.database.table', 'cache');
        $connection = config('cache.stores.database.connection');

        DB::connection($connection)
            ->table($table)
            ->where('key', 'like', $prefixValue.$prefix.'%')
            ->delete();
    }
}