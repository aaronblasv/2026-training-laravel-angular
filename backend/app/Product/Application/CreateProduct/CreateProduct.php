<?php

namespace App\Product\Application\CreateProduct;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\Interfaces\ProductRepositoryInterface;
use App\Product\Domain\ValueObject\ProductName;
use App\Shared\Domain\ValueObject\Uuid;
use App\Product\Domain\ValueObject\ProductPrice;
use App\Product\Domain\ValueObject\ProductStock;

class CreateProduct
{

    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function __invoke(string $name, int $price, int $stock, bool $active, string $familyId, string $taxId): CreateProductResponse
    {
        $product = Product::dddCreate(
            Uuid::generate(),
            ProductName::create($name),
            ProductPrice::create($price),
            ProductStock::create($stock),
            $active,
            $familyId,
            $taxId,
        );

        $this->repository->save($product);

        return CreateProductResponse::create($product);
    }
}