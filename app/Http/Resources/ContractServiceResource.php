<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\LimitType;
use App\Models\ContractService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'ContractResource',
    required: ['id', 'created_at', 'updated_at', 'service_id', 'limit_type', 'limit_value'],
    properties: [
        new Property(
            property: 'id',
            description: 'Record id',
            type: 'integer',
            example: 1
        ),
        new Property(
            property: 'created_at',
            description: 'Creation date in the format YYYY-MM-DD HH:mm:ss',
            type: 'string',
            format: 'date-time',
            example: '2020-01-01 02:43:25'
        ),
        new Property(
            property: 'updated_at',
            description: 'Change date in the format YYYY-MM-DD HH:mm:ss',
            type: 'string',
            format: 'date-time',
            example: '2020-01-02 08:23:11'
        ),
        new Property(
            property: 'service_id',
            description: 'Service id',
            type: 'number',
            example: 456
        ),
        new Property(
            property: 'limit_type',
            description: 'Contract start date',
            type: 'number',
            enum: LimitType::class,
            example: 0
        ),
        new Property(
            property: 'limit_value',
            description: 'Limit value',
            type: 'number',
            example: 10
        ),
    ]
)]
/**
 * @mixin ContractService
 */
class ContractServiceResource extends JsonResource
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
            'service_id' => $this->service_id,
            'limit_type' => $this->limit_type,
            'limit_value' => $this->limit_value
        ];
    }
}
