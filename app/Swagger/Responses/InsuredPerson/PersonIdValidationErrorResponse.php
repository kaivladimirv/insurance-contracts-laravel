<?php

declare(strict_types=1);

namespace App\Swagger\Responses\InsuredPerson;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'PersonIdValidationError',
    title: 'PersonIdValidationError',
    description: 'Insured person id validation error',
    required: ['errors'],
    properties: [
        new Property(
            property: 'errors',
            properties: [
                new Property(
                    property: 'person_id',
                    type: 'array',
                    items: new Items(
                        type: 'string',
                        anyOf: [
                            new Schema(example: 'The person id field is required'),
                            new Schema(example: 'The person id has already been taken'),
                        ]
                    )
                )
            ],
            type: 'object'
        )
    ],
    type: 'object'
)]
class PersonIdValidationErrorResponse
{
}
