<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'CompanyTokenResource',
    required: ['name', 'email', 'created_at', 'updated_at'],
    properties: [
        new Property(
            property: 'name',
            description: 'Company name',
            type: 'string',
            example: 'Company name #1'
        ),
        new Property(
            property: 'email',
            description: 'Company email address',
            type: 'string',
            format: 'email',
            example: 'tester@test.app'
        ),
        new Property(
            property: 'created_at',
            description: 'Company creation date in the format YYYY-MM-DD HH:mm:ss',
            type: 'string',
            format: 'date-time',
            example: '2020-01-01 02:43:25'
        ),
        new Property(
            property: 'updated_at',
            description: 'Company change date in the format YYYY-MM-DD HH:mm:ss',
            type: 'string',
            format: 'date-time',
            example: '020-01-02 08:23:11'
        )
    ]
)]
/**
 * @mixin Company
 */
class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
