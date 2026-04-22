<?php

declare(strict_types=1);

namespace Tests\Unit\Payment;

use App\Payment\Domain\Entity\Payment;
use App\Payment\Domain\Exception\InvalidPaymentMethodException;
use App\Payment\Domain\ValueObject\PaymentMethod;
use App\Shared\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

class PaymentMethodTest extends TestCase
{
    public function test_try_from_hydrates_native_enum(): void
    {
        $method = PaymentMethod::tryFrom('cash');

        $this->assertSame(PaymentMethod::CASH, $method);
        $this->assertTrue($method->isCash());
        $this->assertSame('cash', $method->value);
    }

    public function test_payment_creation_throws_domain_exception_for_invalid_value(): void
    {
        $this->expectException(InvalidPaymentMethodException::class);

        Payment::dddCreate(Uuid::generate(), Uuid::generate(), Uuid::generate(), 1000, 'crypto');
    }
}