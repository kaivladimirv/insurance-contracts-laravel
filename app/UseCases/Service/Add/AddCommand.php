<?php

declare(strict_types=1);

namespace App\UseCases\Service\Add;

use App\Models\Service;
use App\UseCases\Command;
use Illuminate\Validation\Rule;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[Schema(
    title: 'AddCommand',
    required: ['name'],
    properties: [
        new Property(
            property: 'name',
            description: 'Service name',
            type: 'string',
            default: '',
            maxLength: 255,
            example: 'Service #1'
        ),
    ]
)]
class AddCommand extends Data implements Command
{
    public function __construct(
        readonly public int $company_id,
        readonly public string $name
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique(Service::class, 'name')->where('company_id', $context->payload['company_id'])
            ]
        ];
    }
}
