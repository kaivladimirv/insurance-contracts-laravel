<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\NotifierType;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'PersonResource',
    required: ['id', 'name', 'created_at', 'updated_at'],
    properties: [
        new Property(
            property: 'id',
            description: 'Person id',
            type: 'integer',
            example: '1'
        ),
        new Property(
            property: 'created_at',
            description: 'Person creation date in the format YYYY-MM-DD HH:mm:ss',
            type: 'string',
            format: 'date-time',
            example: '2020-01-01 02:43:25'
        ),
        new Property(
            property: 'updated_at',
            description: 'Person change date in the format YYYY-MM-DD HH:mm:ss',
            type: 'string',
            format: 'date-time',
            example: '2020-01-02 08:23:11'
        ),
        new Property(
            property: 'last_name',
            description: 'Last name',
            type: 'string',
            example: 'Ivanov'
        ),
        new Property(
            property: 'first_name',
            description: 'First name',
            type: 'string',
            example: 'Ivan'
        ),
        new Property(
            property: 'middle_name',
            description: 'Middle name',
            type: 'string',
            example: 'Ivanovich'
        ),
        new Property(
            property: 'email',
            description: 'Email',
            type: 'string',
            example: 'ivanov_ivan_91@mail.ru'
        ),
        new Property(
            property: 'phone_number',
            description: 'Phone number',
            type: 'number',
            example: '7773332001'
        ),
        new Property(
            property: 'notifier_type',
            description: 'Notifier type',
            type: 'number',
            enum: NotifierType::class,
            example: 1
        )
    ]
)]
/**
 * @mixin Person
 */
class PersonResource extends JsonResource
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
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'notifier_type' => $this->notifier_type
        ];
    }
}
