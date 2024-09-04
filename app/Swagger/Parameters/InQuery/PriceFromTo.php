<?php

declare(strict_types=1);

namespace App\Swagger\Parameters\InQuery;

use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'priceFrom',
    name: 'price_from',
    description: 'Price from',
    schema: new Schema(type: 'number')
)]
#[QueryParameter(
    parameter: 'priceTo',
    name: 'price_to',
    description: 'Price to',
    schema: new Schema(type: 'number')
)]
/**
 * @psalm-api
 */
class PriceFromTo
{
}
