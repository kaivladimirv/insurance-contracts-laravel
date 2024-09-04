<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\LimitType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'DebtorResource',
    required: ['contract_id', 'insured_person_id', 'service_id', 'limit_type', 'debt'],
    properties: [
        new Property(
            property: 'contract_id',
            description: 'Contract id',
            type: 'integer',
            example: 56
        ),
        new Property(
            property: 'insured_person_id',
            description: 'Insured person id',
            type: 'integer',
            example: 123
        ),
        new Property(
            property: 'service_id',
            description: 'Service id',
            type: 'integer',
            example: 248
        ),
        new Property(
            property: 'limit_type',
            description: 'Limit type',
            type: 'number',
            enum: LimitType::class,
            example: 0
        ),
        new Property(
            property: 'debt',
            description: 'Debt',
            type: 'number',
            example: 5000
        )
    ]
)]
/**
 * @property int $contract_id
 * @property int $insured_person_id
 * @property int $service_id
 * @property int $limit_type
 * @property float $debt
 */
class DebtorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'contract_id' => $this->contract_id,
            'insured_person_id' => $this->insured_person_id,
            'service_id' => $this->service_id,
            'limit_type' => $this->limit_type,
            'debt' => $this->debt
        ];
    }
}
