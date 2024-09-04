<?php

declare(strict_types=1);

namespace Tests\Unit\Balance;

use Database\Factories\BalanceFactory;
use InvalidArgumentException;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    public function testAddValueIsGreaterThanZeroFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        BalanceFactory::new()->makeOne()->add(-1000);
    }

    public function testSubtractValueIsGreaterThanZeroFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        BalanceFactory::new()->makeOne()->subtract(-1000);
    }
}
