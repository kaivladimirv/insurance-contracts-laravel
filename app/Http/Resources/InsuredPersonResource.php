<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\InsuredPerson;
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
            property: 'policy_number',
            description: 'Policy number',
            type: 'string',
            example: 'SDD-02'
        ),
        new Property(
            property: 'is_allowed_to_exceed_limit',
            description: 'Is allowed to exceed limit',
            type: 'boolean',
            enum: [false, true],
            example: false
        ),
        new Property(
            property: 'person_id',
            description: 'Person id',
            type: 'number',
            example: 4
        )
    ]
)]
/**
 * @mixin InsuredPerson
 */
class InsuredPersonResource extends JsonResource
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
            'policy_number' => $this->policy_number,
            'is_allowed_to_exceed_limit' => $this->is_allowed_to_exceed_limit,
            'person_id' => $this->person_id
        ];
    }
}
