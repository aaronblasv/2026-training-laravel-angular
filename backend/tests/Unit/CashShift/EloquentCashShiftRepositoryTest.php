<?php

declare(strict_types=1);

namespace Tests\Unit\CashShift;

use App\CashShift\Domain\Entity\CashShift;
use App\CashShift\Infrastructure\Persistence\Models\EloquentCashShift;
use App\CashShift\Infrastructure\Persistence\Repositories\EloquentCashShiftRepository;
use App\Shared\Domain\ValueObject\Uuid;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Carbon\Carbon;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class EloquentCashShiftRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_update_accepts_carbon_updated_at_when_syncing_persisted_timestamp(): void
    {
        $model = Mockery::mock(EloquentCashShift::class);
        $userModel = Mockery::mock(EloquentUser::class);
        $repository = new EloquentCashShiftRepository($model, $userModel);

        $openedByUserUuid = Uuid::generate();
        $closedByUserUuid = Uuid::generate();

        $cashShift = CashShift::open(Uuid::generate(), 1, $openedByUserUuid, 10000, null);
        $snapshot = \App\CashShift\Domain\ValueObject\ClosingCashSnapshot::create(
            $closedByUserUuid,
            2500,
            1200,
            300,
            0,
            12500,
            'Conteo correcto',
        );
        $cashShift->close($snapshot);

        $closeUserQuery = Mockery::mock();
        $updateQuery = Mockery::mock();
        $freshTimestampQuery = Mockery::mock();

        $userModel->shouldReceive('newQuery')->once()->andReturn($closeUserQuery);
        $closeUserQuery->shouldReceive('where')->once()->with('uuid', $closedByUserUuid->getValue())->andReturnSelf();
        $closeUserQuery->shouldReceive('firstOrFail')->once()->andReturn((object) ['id' => 7]);

        $model->shouldReceive('newQuery')->once()->andReturn($updateQuery);
        $updateQuery->shouldReceive('where')->once()->with('uuid', $cashShift->uuid()->getValue())->andReturnSelf();
        $updateQuery->shouldReceive('when')->once()->with(false, Mockery::type(\Closure::class))->andReturnSelf();
        $updateQuery->shouldReceive('update')->once()->with(Mockery::on(function (array $data) {
            return $data['closed_by_user_id'] === 7
                && $data['status'] === 'closed'
                && $data['cash_total'] === 2500
                && $data['card_total'] === 1200
                && $data['bizum_total'] === 300
                && $data['refund_total'] === 0
                && $data['counted_cash'] === 12500
                && $data['cash_difference'] === 0
                && $data['notes'] === 'Conteo correcto'
                && is_string($data['closed_at']);
        }))->andReturn(1);

        $model->shouldReceive('newQuery')->once()->andReturn($freshTimestampQuery);
        $freshTimestampQuery->shouldReceive('where')->once()->with('uuid', $cashShift->uuid()->getValue())->andReturnSelf();
        $freshTimestampQuery->shouldReceive('value')->once()->with('updated_at')->andReturn(Carbon::parse('2026-04-22 09:10:00'));

        $repository->update($cashShift);

        $this->assertNotNull($cashShift->persistedAt());
        $this->assertSame('2026-04-22 09:10:00', $cashShift->persistedAt()?->format('Y-m-d H:i:s'));
    }
}
