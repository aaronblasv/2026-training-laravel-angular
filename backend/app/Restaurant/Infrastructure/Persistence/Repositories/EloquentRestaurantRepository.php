<?php

declare(strict_types=1);

namespace App\Restaurant\Infrastructure\Persistence\Repositories;

use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;
use Illuminate\Support\Str;

final readonly class EloquentRestaurantRepository implements RestaurantRepositoryInterface
{
    public function __construct(
        private EloquentRestaurant $model,
    ) {}

    public function findNameById(int $restaurantId): ?string
    {
        $name = $this->model->newQuery()
            ->where('id', $restaurantId)
            ->value('name');

        return $name !== null ? (string) $name : null;
    }

    public function create(string $name, string $legalName, string $taxId, string $email): int
    {
        $restaurant = $this->model->newQuery()->create([
            'uuid'       => Str::uuid()->toString(),
            'name'       => $name,
            'legal_name' => $legalName,
            'tax_id'     => $taxId,
            'email'      => $email,
            'password'   => bcrypt(Str::random(16)),
        ]);

        return $restaurant->id;
    }
}

