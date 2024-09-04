<?php

declare(strict_types=1);

namespace App\Swagger\Responses\InsuredPerson;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'InsuredPersonValidationError',
    title: 'InsuredPersonValidationError',
    description: 'Insured person validation error',
    required: ['errors'],
    properties: [
        new Property(
            property: 'errors',
            properties: [
                new Property(
                    property: 'policy_number',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The policy number field is required'),
                            new Schema(example: 'The policy number has already been taken'),
                        ]
                    )
                ),
                new Property(
                    property: 'is_allowed_to_exceed_limit',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The is allowed to exceed limit field is required'),
                            new Schema(example: 'The is allowed to exceed limit field must be true or false'),
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
