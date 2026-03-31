<?php

namespace App\Product\Application\DeactivateProduct;

use App\Product\Domain\Interfaces\ProductRepositoryInterface;

class DeactivateProduct
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function __invoke(string $uuid): void
    {
        $product = $this->repository->findById($uuid);

        if ($product === null) {
            throw new \Exception('Product not found');
        }

        $product->dddUpdate(
            $product->getName(),
            $product->getPrice(),
            $product->getStock(),
            false,
            $product->getFamilyId(),
            $product->getTaxId(),
        );

        $this->repository->save($product);
    }
}