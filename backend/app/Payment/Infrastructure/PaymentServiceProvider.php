<?php

namespace App\Payment\Infrastructure;

use App\Payment\Domain\Interfaces\PaymentRepositoryInterface;
use App\Payment\Infrastructure\Persistence\Repositories\EloquentPaymentRepository;
use App\Payment\Application\RegisterPayment\RegisterPayment;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            PaymentRepositoryInterface::class,
            EloquentPaymentRepository::class
        );

        $this->app->bind(RegisterPayment::class, function ($app) {
            return new RegisterPayment(
                $app->make(PaymentRepositoryInterface::class)
            );
        });
    }
}

