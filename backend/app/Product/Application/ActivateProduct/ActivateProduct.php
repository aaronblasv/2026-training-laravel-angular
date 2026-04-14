<?php

namespace App\Product\Application\ActivateProduct;

use App\Product\Domain\Interfaces\ProductRepositoryInterface;

class ActivateProduct
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function __invoke(string $uuid, int $restaurantId): void
    {
        $product = $this->repository->findById($uuid, $restaurantId);

        if ($product === null) {
            throw new \Exception('Product not found');
        }

        $product->dddUpdate(
            $product->name(),
            $product->price(),
            $product->stock(),
            true,
            $product->familyId(),
            $product->taxId(),
            $product->imageSrc(),
        );

        $this->repository->save($product);
    }
}
