<?php

namespace App\Log\Infrastructure;

use App\Log\Domain\Interfaces\LogRepositoryInterface;
use App\Log\Infrastructure\Persistence\Repositories\EloquentLogRepository;
use App\Log\Application\CreateLog\CreateLog;
use App\Log\Application\GetLogs\GetLogs;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            LogRepositoryInterface::class,
            EloquentLogRepository::class
        );

        $this->app->bind(CreateLog::class, function ($app) {
            return new CreateLog(
                $app->make(LogRepositoryInterface::class)
            );
        });

        $this->app->bind(GetLogs::class, function ($app) {
            return new GetLogs(
                $app->make(LogRepositoryInterface::class)
            );
        });
    }
}
