<?php

namespace App\Product\Application\CreateProduct;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\Interfaces\ProductRepositoryInterface;
use App\Product\Domain\ValueObject\ProductName;
use App\Shared\Domain\ValueObject\Uuid;
use App\Product\Domain\ValueObject\ProductPrice;
use App\Product\Domain\ValueObject\ProductStock;

final readonly class CreateProductResponse
{
    private function __construct(
        public string $uuid,
        public string $name,
        public float $price,
        public int $stock,
    ) {}

    public static function create(Product $product): self
    {
        return new self(
            $product->getUuid()->getValue(),
            $product->getName()->getValue(),
            $product->getPrice()->getValue(),
            $product->getStock()->getValue(),
        );
    }
}