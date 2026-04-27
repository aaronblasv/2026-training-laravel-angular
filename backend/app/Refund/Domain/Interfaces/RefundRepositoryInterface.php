<?php

declare(strict_types=1);

namespace App\Refund\Domain\Interfaces;

use App\Refund\Domain\Entity\Refund;
use App\Refund\Domain\Entity\RefundLine;

interface RefundRepositoryInterface
{
    public function save(Refund $refund): void;
    public function saveLine(RefundLine $line): void;
    public function saveLinesBatch(array $lines): void;
}