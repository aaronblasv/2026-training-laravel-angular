<?php

namespace App\Product\Application\CreateProduct;

use App\Product\Domain\Entity\Product;

final readonly class CreateProductResponse
{
    private function __construct(
        public string $uuid,
        public string $name,
        public int $price,
        public int $stock,
        public bool $active,
        public string $familyId,
        public string $taxId,
        public ?string $imageSrc = null,
    ) {}

    public static function create(Product $product): self
    {
        return new self(
            $product->uuid()->getValue(),
            $product->name()->getValue(),
            $product->price()->getValue(),
            $product->stock()->getValue(),
            $product->active(),
            $product->familyId()->getValue(),
            $product->taxId()->getValue(),
            $product->imageSrc(),
        );
    }
}
