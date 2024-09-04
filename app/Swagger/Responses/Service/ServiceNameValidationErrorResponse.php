<?php

declare(strict_types=1);

namespace App\Swagger\Responses\Service;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'ServiceNameValidationError',
    description: 'Name validation error',
    required: ['errors'],
    properties: [
        new Property(
            property: 'errors',
            properties: [
                new Property(
                    property: 'name',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The name field is required.'),
                            new Schema(example: 'The name field must not be greater than 255 characters.'),
                            new Schema(example: 'The name has already been taken.'),
                        ]
                    )
                ),
            ],
            type: 'object'
        )
    ],
    type: 'object'
)]
class ServiceNameValidationErrorResponse
{
}
