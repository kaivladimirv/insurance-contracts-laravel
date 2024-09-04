<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'ContractResource',
    required: ['id', 'created_at', 'updated_at', 'number', 'start_date', 'end_date', 'max_amount'],
    properties: [
        new Property(
            property: 'id',
            description: 'Contract id',
            type: 'integer',
            example: 1
        ),
        new Property(
            property: 'created_at',
            description: 'Contract creation date in the format YYYY-MM-DD HH:mm:ss',
            type: 'string',
            format: 'date-time',
            example: '2020-01-01 02:43:25'
        ),
        new Property(
            property: 'updated_at',
            description: 'Contract change date in the format YYYY-MM-DD HH:mm:ss',
            type: 'string',
            format: 'date-time',
            example: '2020-01-02 08:23:11'
        ),
        new Property(
            property: 'number',
            description: 'Contract number',
            type: 'string',
            example: 'DK-12-6'
        ),
        new Property(
            property: 'start_date',
            description: 'Contract start date',
            type: 'string',
            example: '2023-01-12'
        ),
        new Property(
            property: 'end_date',
            description: 'Contract end date',
            type: 'string',
            example: '2024-01-12'
        ),
        new Property(
            property: 'max_amount',
            description: 'Max amount',
            type: 'number',
            example: 13213234
        )
    ]
)]
/**
 * @mixin Contract
 */
class ContractResource extends JsonResource
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
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'number' => $this->number,
            'start_date' => $this->start_date->toDateString(),
            'end_date' => $this->end_date->toDateString(),
            'max_amount' => $this->max_amount,
        ];
    }
}
