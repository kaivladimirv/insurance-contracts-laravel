<?php

declare(strict_types=1);

namespace App\UseCases\InsuredPerson\Add;

use App\Models\InsuredPerson;
use App\Models\Person;
use App\UseCases\Command;
use Illuminate\Validation\Rule;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[Schema(
    schema: 'InsuredPersonAddCommand',
    title: 'InsuredPersonAddCommand',
    required: ['person_id', 'policy_number', 'is_allowed_to_exceed_limit'],
    properties: [
        new Property(
            property: 'person_id',
            description: 'Person id',
            type: 'integer',
            example: 1
        ),
        new Property(
            property: 'policy_number',
            description: 'Policy number',
            type: 'string',
            maxLength: 30,
            example: 'SDD-01'
        ),
        new Property(
            property: 'is_allowed_to_exceed_limit',
            description: 'Determines whether it is allowed to exceed the limits or not',
            type: 'integer',
            enum: [0, 1]
        )
    ]
)]
class AddCommand extends Data implements Command
{
    public function __construct(
        readonly public int $company_id,
        #[FromRouteParameter('contract_id')]
        readonly public int $contract_id,
        readonly public int $person_id,
        readonly public string $policy_number,
        #[Required]
        readonly public bool $is_allowed_to_exceed_limit
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'person_id' => [
                'required',
                'numeric',
                Rule::exists(Person::class, 'id')->where('company_id', $context->payload['company_id']),
                Rule::unique(InsuredPerson::class, 'person_id')->where('contract_id', $context->payload['contract_id'])
            ],
            'policy_number' => [
                'required',
                'max:30',
                Rule::unique(InsuredPerson::class, 'policy_number')->where('contract_id', $context->payload['contract_id'])
            ]
        ];
    }
}
