<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\LimitType;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class LimitTypeCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): LimitType
    {
        return LimitType::from((int) $value);
    }
}
