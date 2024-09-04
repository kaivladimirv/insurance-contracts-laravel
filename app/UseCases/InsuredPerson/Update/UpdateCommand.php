<?php

declare(strict_types=1);

namespace App\UseCases\InsuredPerson\Update;

use App\Models\InsuredPerson;
use App\UseCases\Command;
use Illuminate\Validation\Rule;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[Schema(
    schema: 'InsuredPersonUpdateCommand',
    title: 'InsuredPersonUpdateCommand',
    required: ['policy_number', 'is_allowed_to_exceed_limit'],
    properties: [
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
class UpdateCommand extends Data implements Command
{
    public function __construct(
        #[FromRouteParameter('contract_id')]
        readonly public int $contract_id,
        #[FromRouteParameter('insured_person_id')]
        readonly public int $insured_person_id,
        readonly public string $policy_number,
        #[Required]
        readonly public bool $is_allowed_to_exceed_limit
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'policy_number' => [
                'required',
                'max:30',
                Rule::unique(InsuredPerson::class, 'policy_number')
                    ->where('contract_id', $context->payload['contract_id'])
                    ->ignore($context->payload['insured_person_id'])
            ]
        ];
    }
}
