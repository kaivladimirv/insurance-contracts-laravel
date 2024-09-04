<?php

declare(strict_types=1);

namespace App\Enums;

enum LimitType: int
{
    case SUM = 0;
    case QUANTITY = 1;

    public function isItQuantityLimiter(): bool
    {
        return $this === self::QUANTITY;
    }

    public function isItAmountLimiter(): bool
    {
        return $this === self::SUM;
    }
}
