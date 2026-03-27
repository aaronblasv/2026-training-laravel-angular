<?php

namespace App\Product\Application\DeleteProduct;

use App\Product\Domain\Interfaces\ProductRepositoryInterface;

class DeleteProduct
{

    private ProductRepositoryInterface $repository;

    public function __construct(ProductRepositoryInterface $repository) 
    {
        $this->repository = $repository;
    }

    public function __invoke(string $uuid): void 
    {
        
        $product = $this->repository->findById($uuid);

        if(!$product) {
            throw new \Exception('Product not found');
        }

        $this->repository->delete($uuid);
        
    }
}