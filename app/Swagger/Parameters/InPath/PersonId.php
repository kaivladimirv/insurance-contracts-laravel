<?php

declare(strict_types=1);

namespace App\Swagger\Parameters\InPath;

use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Schema;

#[PathParameter(
    parameter: 'personId',
    name: 'personId',
    description: 'Person id',
    required: true,
    schema: new Schema(type: 'integer')
)]
/**
 * @psalm-api
 */
class PersonId
{
}
