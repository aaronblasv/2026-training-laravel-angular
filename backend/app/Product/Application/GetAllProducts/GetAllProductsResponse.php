<?php

namespace App\Product\Application\GetAllProducts;

use App\Product\Domain\Interfaces\ProductRepositoryInterface;
use App\Product\Domain\Entity\Product;

final readonly class GetAllProductsResponse
{
    private function __construct(
        public string $uuid,
        public string $name,
        public int $stock,
        public int $price,
        public bool $active,
        public string $familyId,
        public string $taxId,
    ) {}

    public static function create(Product $product): self
    {
        return new self(
            $product->getUuid()->getValue(),
            $product->getName()->getValue(),
            $product->getStock()->getValue(),
            $product->getPrice()->getValue(),
            $product->isActive(),
            $product->getFamilyId(),
            $product->getTaxId(),
        );
    }
}