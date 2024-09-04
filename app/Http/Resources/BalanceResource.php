<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\LimitType;
use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'BalanceResource',
    required: ['service_id', 'limit_type', 'balance'],
    properties: [
        new Property(
            property: 'service_id',
            description: 'Service id',
            type: 'integer',
            example: 105
        ),
        new Property(
            property: 'limit_type',
            description: 'Limit type',
            type: 'integer',
            enum: LimitType::class,
            example: 0
        ),
        new Property(
            property: 'balance',
            description: 'Balance',
            type: 'double',
            example: 10
        )
    ]
)]
/**
 * @mixin Balance
 */
class BalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'service_id' => $this->service_id,
            'limit_type' => $this->limit_type,
            'balance' => $this->balance,
        ];
    }
}
