<?php

declare(strict_types=1);

namespace App\Swagger\Responses\Company;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'CompanyEmailValidationError',
    description: 'Email validation error',
    required: ['errors'],
    properties: [
        new Property(
            property: 'errors',
            properties: [
                new Property(
                    property: 'email',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The email field is required.'),
                            new Schema(example: 'The email field must not be greater than 255 characters.'),
                            new Schema(example: 'The email field must be a valid email address.'),
                            new Schema(example: 'The email has already been taken.'),
                        ]
                    )
                ),
            ],
            type: 'object'
        )
    ],
    type: 'object'
)]
class EmailValidationErrorResponse
{
}
