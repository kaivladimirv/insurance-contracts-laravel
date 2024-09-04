<?php

declare(strict_types=1);

namespace App\Swagger\Responses;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'CollectionResponse',
    description: 'Collection response',
    required: ['data', 'links', 'meta', 'path', 'per_page', 'total'],
    properties: [
        new Property(
            property: 'data'
        ),
        new Property(
            property: 'links',
            properties: [
                new Property(property: 'first', description: 'URL for the first page', type: 'string'),
                new Property(property: 'last', description: 'URL for the last page', type: 'string'),
                new Property(property: 'prev', description: 'URL for the previous page', type: 'string'),
                new Property(property: 'next', description: 'URL for the next page', type: 'string'),
            ],
            type: 'object'
        ),
        new Property(
            property: 'meta',
            properties: [
                new Property(property: 'current_page', description: 'Current page number', type: 'integer'),
                new Property(property: 'from', description: 'First page number', type: 'integer'),
                new Property(property: 'last_page', description: 'Last page number', type: 'integer')
            ],
            type: 'object'
        ),
        new Property(property: 'path', description: 'The base path', type: 'string'),
        new Property(property: 'per_page', description: 'The number of items to be shown per page', type: 'integer'),
        new Property(property: 'total', description: 'The total number of matching items in the data store', type: 'integer')
    ],
    type: 'object'
)]
class CollectionResponse
{
}
