<?php

declare(strict_types=1);

namespace App\Swagger\Responses\Debtors;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'DebtorsValidationError',
    title: 'DebtorsValidationError',
    description: 'Debtors validation error',
    required: ['errors'],
    properties: [
        new Property(
            property: 'errors',
            properties: [
                new Property(
                    property: 'page',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The page field is required'),
                            new Schema(example: 'The page field must be an integer'),
                            new Schema(example: 'The page field must be at least 1')
                        ]
                    )
                ),
                new Property(
                    property: 'service_id',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The selected service id is invalid'),
                            new Schema(example: 'The service id field must be an integer'),
                        ]
                    )
                ),
                new Property(
                    property: 'debt_from',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The debt from field must be an integer'),
                            new Schema(example: 'The debt from field must be greater than 0')
                        ]
                    )
                ),
                new Property(
                    property: 'debt_to',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The debt to field must be an integer'),
                            new Schema(example: 'The debt to field must be greater than 0')
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
