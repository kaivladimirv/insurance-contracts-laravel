<?php

declare(strict_types=1);

namespace App\Swagger\Responses\Contract;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'ContractValidationError',
    title: 'ContractValidationError',
    description: 'Contract validation error',
    required: ['errors'],
    properties: [
        new Property(
            property: 'errors',
            properties: [
                new Property(
                    property: 'number',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The number field is required'),
                            new Schema(example: 'The number field must not be greater than 50 characters'),
                            new Schema(example: 'The number has already been taken'),
                        ]
                    )
                ),
                new Property(
                    property: 'start_date',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The start date field is required'),
                            new Schema(example: 'The start date field must be a valid date'),
                        ]
                    )
                ),
                new Property(
                    property: 'end_date',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The end date field is required'),
                            new Schema(example: 'The start date field must be a valid date'),
                            new Schema(example: 'The end date field must be a date after or equal to start date'),
                        ]
                    )
                ),
                new Property(
                    property: 'max_amount',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The max amount field is required'),
                            new Schema(example: 'The max amount field must be at least 0'),
                        ]
                    )
                )
            ],
            type: 'object'
        )
    ],
    type: 'object'
)]
class ValidationErrorResponse
{
}
