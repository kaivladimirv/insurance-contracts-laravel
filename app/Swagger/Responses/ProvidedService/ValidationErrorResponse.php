<?php

declare(strict_types=1);

namespace App\Swagger\Responses\ProvidedService;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'ProvidedServiceValidationError',
    title: 'ProvidedServiceValidationError',
    description: 'Provided service validation error',
    required: ['errors'],
    properties: [
        new Property(
            property: 'errors',
            properties: [
                new Property(
                    property: 'service_id',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The service id field is required'),
                        ]
                    )
                ),
                new Property(
                    property: 'date_of_service',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The date of service field is required'),
                            new Schema(example: 'The date of service field must be a valid date'),
                        ]
                    )
                ),
                new Property(
                    property: 'quantity',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The quantity field is required'),
                            new Schema(example: 'The quantity field must be greater than 0'),
                        ]
                    )
                ),
                new Property(
                    property: 'price',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The price field is required'),
                            new Schema(example: 'The quantity field must be greater than 0'),
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
