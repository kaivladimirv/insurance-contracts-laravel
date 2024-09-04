<?php

declare(strict_types=1);

namespace App\Swagger\Parameters\InQuery;

use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'serviceName',
    name: 'service_name',
    description: 'Service name',
    schema: new Schema(type: 'string', maxLength: 255)
)]
/**
 * @psalm-api
 */
class ServiceName
{
}
