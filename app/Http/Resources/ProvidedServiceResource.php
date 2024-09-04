<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\LimitType;
use App\Models\ProvidedService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'ProvidedServiceResource',
    required: ['id', 'created_at', 'date_of_service', 'contract_id', 'insured_person_id', 'service_id', 'service_name', 'limit_type', 'quantity', 'price', 'amount'],
    properties: [
        new Property(
            property: 'id',
            description: 'Provided service id',
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
            property: 'date_of_service',
            description: 'Service provision date in the format YYYY-MM-DD',
            type: 'string',
            format: 'date-time',
            example: '2020-01-02'
        ),
        new Property(
            property: 'contract_id',
            description: 'Contract id',
            type: 'integer',
            example: 256
        ),
        new Property(
            property: 'insured_person_id',
            description: 'Insured person id',
            type: 'integer',
            example: 1045
        ),
        new Property(
            property: 'service_id',
            description: 'Service id',
            type: 'integer',
            example: 89
        ),
        new Property(
            property: 'service_name',
            description: 'Service name',
            type: 'string',
            maxLength: 255,
            example: 'Service name #4'
        ),
        new Property(
            property: 'limit_type',
            description: 'Limit type',
            type: 'number',
            enum: LimitType::class,
            example: 1
        ),
        new Property(
            property: 'quantity',
            description: 'Quantity',
            type: 'number',
            minimum: 0,
            example: 1
        ),
        new Property(
            property: 'price',
            description: 'Price',
            type: 'number',
            minimum: 0,
            example: 15000
        ),
        new Property(
            property: 'amount',
            description: 'Amount',
            type: 'number',
            minimum: 0,
            example: 15000
        )
    ]
)]
/**
 * @mixin ProvidedService
 */
class ProvidedServiceResource extends JsonResource
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
            'date_of_service' => $this->date_of_service->toDateString(),
            'contract_id' => $this->contract_id,
            'insured_person_id' => $this->insured_person_id,
            'service_id' => $this->service_id,
            'service_name' => $this->service_name,
            'limit_type' => $this->limit_type,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'amount' => $this->amount
        ];
    }
}
