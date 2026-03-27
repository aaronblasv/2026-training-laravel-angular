<?php

namespace App\Product\Application\GetAllProducts;

use App\Product\Domain\Interfaces\ProductRepositoryInterface;
use App\Product\Domain\Entity\Product;

class GetAllProducts
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function __invoke(): array
    {
        $product = $this->repository->findAll();

        return array_map(
            fn(Product $product) => GetAllProductsResponse::create($product),
            $product
        );
    }
}