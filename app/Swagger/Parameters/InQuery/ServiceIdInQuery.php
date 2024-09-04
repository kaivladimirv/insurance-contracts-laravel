<?php

declare(strict_types=1);

namespace App\Swagger\Parameters\InQuery;

use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'serviceIdInQuery',
    name: 'serviceId',
    description: 'Service id',
    schema: new Schema(type: 'integer')
)]
/**
 * @psalm-api
 */
class ServiceIdInQuery
{
}
