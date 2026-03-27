<?php

namespace App\Product\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;
use App\Product\Domain\ValueObject\ProductName;
use App\Product\Domain\ValueObject\ProductPrice;
use App\Product\Domain\ValueObject\ProductStock;

class Product
{

    private function __construct(
        private Uuid $uuid,
        private ProductName $name,
        private ProductPrice $price,
        private ProductStock $stock,
        private bool $active,
        private string $familyId,
        private string $taxId,
    ) {}

    public static function dddCreate(
        Uuid $uuid,
        ProductName $name,
        ProductPrice $price,
        ProductStock $stock,
        bool $active,
        string $familyId,
        string $taxId,
    ): self {
        return new self($uuid, $name, $price, $stock, $active, $familyId, $taxId);
    }
    
    public function dddUpdate(
        ProductName $name,
        ProductPrice $price,
        ProductStock $stock,
        bool $active,
        string $familyId,
        string $taxId,

    ): void {
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
        $this->active = $active;
        $this->familyId = $familyId;
        $this->taxId = $taxId;
    }
    
    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getName(): ProductName
    {
        return $this->name;
    }

    public function getPrice(): ProductPrice
    {
        return $this->price;
    }

    public function getStock(): ProductStock
    {
        return $this->stock;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getFamilyId(): string
    {
        return $this->familyId;
    }

    public function getTaxId(): string
    {
        return $this->taxId;
    }
}