<?php

declare(strict_types=1);

namespace App\Swagger\Responses;

use OpenApi\Attributes\Schema;

#[Schema(
    description: 'Invalid token',
)]
class InvalidTokenResponse
{
}
