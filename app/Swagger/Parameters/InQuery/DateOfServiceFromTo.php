<?php

declare(strict_types=1);

namespace App\Swagger\Parameters\InQuery;

use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'dateOfServiceFrom',
    name: 'date_of_service_from',
    description: 'Date of service from in the format YYYY-MM-DD',
    schema: new Schema(type: 'date')
)]
#[QueryParameter(
    parameter: 'dateOfServiceTo',
    name: 'date_of_service_to',
    description: 'Date of service to in the format YYYY-MM-DD',
    schema: new Schema(type: 'date')
)]
/**
 * @psalm-api
 */
class DateOfServiceFromTo
{
}
