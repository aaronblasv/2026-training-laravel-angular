<?php

namespace App\Product\Application\UpdateProduct;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\Interfaces\ProductRepositoryInterface;
use App\Product\Domain\ValueObject\ProductName;
use App\Shared\Domain\ValueObject\Uuid;
use App\Product\Domain\ValueObject\ProductPrice;
use App\Product\Domain\ValueObject\ProductStock;

class UpdateProduct
{
    private ProductRepositoryInterface $repository;

    public function __construct(ProductRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function __invoke(string $uuid, string $name, int $price, int $stock, bool $active, string $familyId, string $taxId): UpdateProductResponse
    {
        $product = $this->repository->findById($uuid);

        if (!$product) {
            throw new \Exception('Product not found');
        }

        $product->dddUpdate(
            ProductName::create($name),
            ProductPrice::create($price),
            ProductStock::create($stock),
            $active,
            $familyId,
            $taxId,
        );

        $this->repository->save($product);

        return UpdateProductResponse::create($product);
    }
}