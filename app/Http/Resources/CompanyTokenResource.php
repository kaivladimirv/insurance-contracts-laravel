<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'CompanyTokenResource',
    required: ['token_type', 'access_token'],
    properties: [
        new Property(
            property: 'token_type',
            description: 'Token type',
            type: 'string',
            enum: ['Bearer'],
            example: 'Bearer'
        ),
        new Property(
            property: 'access_token',
            description: 'Access token',
            type: 'string',
            example: '11|C6AapOjoIutFG4Ydm08CosNws7vfQLoNalDlu2'
        )
    ]
)]
/**
 * @mixin NewAccessToken
 */
class CompanyTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token_type' => 'Bearer',
            'access_token' => $this->plainTextToken
        ];
    }
}
