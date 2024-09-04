<?php

declare(strict_types=1);

namespace App\Swagger\Responses\ContractService;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'LimitValidationError',
    title: 'LimitValidationError',
    description: 'Limit validation error',
    required: ['errors'],
    properties: [
        new Property(
            property: 'errors',
            properties: [
                new Property(
                    property: 'limit_type',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The limit type field is required'),
                            new Schema(example: 'The selected limit type is invalid'),
                        ]
                    )
                ),
                new Property(
                    property: 'limit_value',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The limit value field is required'),
                            new Schema(example: 'The limit value field must be at least 0'),
                        ]
                    )
                ),
            ],
            type: 'object'
        )
    ],
    type: 'object'
)]
class LimitValidationErrorResponse
{
}
