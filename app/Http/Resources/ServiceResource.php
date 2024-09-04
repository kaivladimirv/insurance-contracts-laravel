<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'ServiceResource',
    required: ['id', 'name', 'created_at', 'updated_at'],
    properties: [
        new Property(
            property: 'id',
            description: 'Service id',
            type: 'integer',
            example: '1'
        ),
        new Property(
            property: 'name',
            description: 'Company name',
            type: 'string',
            example: 'Company name #1'
        ),
        new Property(
            property: 'created_at',
            description: 'Service creation date in the format YYYY-MM-DD HH:mm:ss',
            type: 'string',
            format: 'date-time',
            example: '2020-01-01 02:43:25'
        ),
        new Property(
            property: 'updated_at',
            description: 'Service change date in the format YYYY-MM-DD HH:mm:ss',
            type: 'string',
            format: 'date-time',
            example: '020-01-02 08:23:11'
        )
    ]
)]
/**
 * @mixin Service
 */
class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
