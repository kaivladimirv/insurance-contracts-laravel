<?php

declare(strict_types=1);

namespace App\Swagger\Responses\ContractService;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'ServiceIdValidationError',
    title: 'ServiceIdValidationError',
    description: 'Service id validation error',
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
                            new Schema(example: 'The selected service id is invalid'),
                            new Schema(example: 'The service id has already been taken'),
                        ]
                    )
                )
            ],
            type: 'object'
        )
    ],
    type: 'object'
)]
class ServiceIdValidationErrorResponse
{
}
