<?php

declare(strict_types=1);

namespace Tests\Unit\User;

use App\Shared\Domain\ValueObject\Uuid;
use App\User\Application\UpdateUserPhoto\UpdateUserPhoto;
use App\User\Domain\Entity\User;
use App\User\Domain\Interfaces\UserRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class UpdateUserPhotoTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_updates_user_photo_through_repository(): void
    {
        $restaurantId = 7;
        $userUuid = Uuid::generate()->getValue();

        $user = User::fromPersistence(
            $userUuid,
            'María',
            'maria@example.com',
            password_hash('secret', PASSWORD_BCRYPT),
            'admin',
            $restaurantId,
            '1234',
            null,
            new \DateTimeImmutable('2026-04-01 10:00:00'),
            new \DateTimeImmutable('2026-04-01 10:00:00'),
        );

        $repository = Mockery::mock(UserRepositoryInterface::class);
        $repository->shouldReceive('findById')->once()->with($userUuid, $restaurantId)->andReturn($user);
        $repository->shouldReceive('save')->once()->with(Mockery::on(
            fn (User $savedUser): bool => $savedUser->uuid()->getValue() === $userUuid
                && $savedUser->imageSrc() === '/storage/users/maria.png'
        ));

        $response = (new UpdateUserPhoto($repository))($userUuid, $restaurantId, '/storage/users/maria.png');

        $this->assertSame($userUuid, $response->uuid);
        $this->assertSame('/storage/users/maria.png', $response->imageSrc);
    }
}