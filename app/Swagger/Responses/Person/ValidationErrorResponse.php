<?php

declare(strict_types=1);

namespace App\Swagger\Responses\Person;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'PersonValidationError',
    title: 'PersonValidationError',
    description: 'Person validation error',
    required: ['errors'],
    properties: [
        new Property(
            property: 'errors',
            properties: [
                new Property(
                    property: 'last_name',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The middle name field is required'),
                            new Schema(example: 'The middle name field must not be greater than 255 characters'),
                        ]
                    )
                ),
                new Property(
                    property: 'first_name',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The middle name field is required'),
                            new Schema(example: 'The middle name field must not be greater than 255 characters'),
                        ]
                    )
                ),
                new Property(
                    property: 'middle_name',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The middle name field is required'),
                            new Schema(example: 'The middle name field must not be greater than 255 characters'),
                        ]
                    )
                ),
                new Property(
                    property: 'email',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The email field must not be greater than 255 characters'),
                            new Schema(example: 'The email has already been taken'),
                            new Schema(example: 'The email field must be a valid email address'),
                            new Schema(example: 'The email field is required when notifier type is 0'),
                        ]
                    )
                ),
                new Property(
                    property: 'phone_number',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The phone number field must not be greater than 15 characters'),
                            new Schema(example: 'The phone number has already been taken'),
                            new Schema(example: 'The phone number field is required when notifier type is 1'),
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
