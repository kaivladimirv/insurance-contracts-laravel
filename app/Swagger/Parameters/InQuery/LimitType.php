<?php

declare(strict_types=1);

namespace App\Swagger\Parameters\InQuery;

use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'limitType',
    name: 'limit_type',
    description: 'Limit type',
    schema: new Schema(type: 'number', enum: \App\Enums\LimitType::class)
)]
/**
 * @psalm-api
 */
class LimitType
{
}
