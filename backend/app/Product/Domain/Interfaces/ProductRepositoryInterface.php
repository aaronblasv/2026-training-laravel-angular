<?php

namespace App\Product\Domain\Interfaces;

use App\Product\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function findById(string $uuid): ?Product;
    public function delete(string $id): void;
}