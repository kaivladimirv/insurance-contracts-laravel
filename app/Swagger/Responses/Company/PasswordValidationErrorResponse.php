<?php

declare(strict_types=1);

namespace App\Swagger\Responses\Company;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'CompanyPasswordValidationError',
    description: 'Password validation error',
    required: ['errors'],
    properties: [
        new Property(
            property: 'errors',
            properties: [
                new Property(
                    property: 'password',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The password field is required.'),
                            new Schema(example: 'The password field must be at least 8 characters.'),
                            new Schema(example: 'The password field must not be greater than 255 characters.'),
                            new Schema(example: 'The password field must contain at least one uppercase and one lowercase letter.'),
                            new Schema(example: 'The password field must contain at least one letter.'),
                            new Schema(example: 'The password field must contain at least one symbol.'),
                            new Schema(example: 'The password field must contain at least one number.'),
                            new Schema(example: 'The password field confirmation does not match.')
                        ]
                    )
                )
            ],
            type: 'object'
        )
    ],
    type: 'object',
)]
class PasswordValidationErrorResponse
{
}
