<?php

declare(strict_types=1);

namespace App\Swagger\Parameters\InQuery;

use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Schema;

#[QueryParameter(
    parameter: 'pageNumber',
    name: 'page',
    description: 'Page number',
    schema: new Schema(type: 'integer', minimum: 1, example: 1)
)]
class PageNumber
{
}
