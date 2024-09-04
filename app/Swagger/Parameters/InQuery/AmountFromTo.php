<?php

declare(strict_types=1);

namespace App\Swagger\Parameters\InQuery;

use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'amountFrom',
    name: 'amount_from',
    description: 'Amount from',
    schema: new Schema(type: 'number')
)]
#[QueryParameter(
    parameter: 'amountTo',
    name: 'amount_to',
    description: 'Amount to',
    schema: new Schema(type: 'number')
)]
/**
 * @psalm-api
 */
class AmountFromTo
{
}
