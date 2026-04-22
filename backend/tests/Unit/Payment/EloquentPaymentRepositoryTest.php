<?php

declare(strict_types=1);

namespace Tests\Unit\Payment;

use App\Order\Domain\Exception\OrderNotFoundException;
use App\Payment\Domain\Entity\Payment;
use App\Payment\Infrastructure\Persistence\Models\EloquentPayment;
use App\Payment\Infrastructure\Persistence\Repositories\EloquentPaymentRepository;
use App\Shared\Domain\ValueObject\Uuid;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use App\Order\Infrastructure\Persistence\Models\EloquentOrder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class EloquentPaymentRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_save_throws_order_not_found_exception_when_order_is_missing(): void
    {
        $model = Mockery::mock(EloquentPayment::class);
        $orderModel = Mockery::mock(EloquentOrder::class);
        $userModel = Mockery::mock(EloquentUser::class);
        $repository = new EloquentPaymentRepository($model, $orderModel, $userModel);

        $payment = Payment::dddCreate(Uuid::generate(), Uuid::generate(), Uuid::generate(), 1000, 'cash');

        $orderQuery = Mockery::mock();
        $userQuery = Mockery::mock();

        $this->mockOrderLookup($orderModel, $orderQuery, null);
        $this->mockUserLookup($userModel, $userQuery, (object) ['id' => 1]);

        $this->expectException(OrderNotFoundException::class);

        $repository->save($payment);
    }

    public function test_save_throws_user_not_found_exception_when_user_is_missing(): void
    {
        $model = Mockery::mock(EloquentPayment::class);
        $orderModel = Mockery::mock(EloquentOrder::class);
        $userModel = Mockery::mock(EloquentUser::class);
        $repository = new EloquentPaymentRepository($model, $orderModel, $userModel);

        $payment = Payment::dddCreate(Uuid::generate(), Uuid::generate(), Uuid::generate(), 1000, 'cash');

        $orderQuery = Mockery::mock();
        $userQuery = Mockery::mock();

        $this->mockOrderLookup($orderModel, $orderQuery, (object) ['id' => 2]);
        $this->mockUserLookup($userModel, $userQuery, null);

        $this->expectException(UserNotFoundException::class);

        $repository->save($payment);
    }

    private function mockOrderLookup(EloquentOrder $orderModel, object $query, ?object $result): void
    {
        $orderModel->shouldReceive('newQuery')->once()->andReturn($query);
        $query->shouldReceive('where')->once()->with('uuid', Mockery::type('string'))->andReturnSelf();
        $query->shouldReceive('first')->once()->andReturn($result);
    }

    private function mockUserLookup(EloquentUser $userModel, object $query, ?object $result): void
    {
        $userModel->shouldReceive('newQuery')->once()->andReturn($query);
        $query->shouldReceive('where')->once()->with('uuid', Mockery::type('string'))->andReturnSelf();
        $query->shouldReceive('first')->once()->andReturn($result);
    }
}