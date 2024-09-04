<?php

declare(strict_types=1);

namespace App\Swagger\Parameters\InQuery;

use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'quantityFrom',
    name: 'quantity_from',
    description: 'Quantity from',
    schema: new Schema(type: 'number')
)]
#[QueryParameter(
    parameter: 'quantityTo',
    name: 'quantity_to',
    description: 'Quantity to',
    schema: new Schema(type: 'number')
)]
/**
 * @psalm-api
 */
class QuantityFromTo
{
}
