<?php

declare(strict_types=1);

namespace App\UseCases\ContractService\Add;

use App\Enums\LimitType;
use App\Models\ContractService;
use App\Models\Service;
use App\UseCases\Command;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[Schema(
    schema: 'ContractServiceAddCommand',
    title: 'ContractServiceAddCommand',
    required: ['service_id', 'limit_type', 'limit_value'],
    properties: [
        new Property(
            property: 'service_id',
            description: 'Service id',
            type: 'integer',
            example: '1'
        ),
        new Property(
            property: 'limit_type',
            description: 'Limit type (0 - on amount, 1 - on quantity)',
            type: 'integer',
            enum: LimitType::class,
            example: 1
        ),
        new Property(
            property: 'limit_value',
            description: 'Limit value',
            type: 'number',
            minimum: 0,
            example: 10
        )
    ]
)]
class AddCommand extends Data implements Command
{
    public function __construct(
        readonly public int $company_id,
        #[FromRouteParameter('contract_id')]
        readonly public int $contract_id,
        readonly public int $service_id,
        readonly public LimitType $limit_type,
        #[Numeric]
        #[Min(0)]
        readonly public int|float $limit_value
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'service_id' => [
                'required',
                'numeric',
                Rule::exists(Service::class, 'id')->where('company_id', $context->payload['company_id']),
                Rule::unique(ContractService::class, 'service_id')->where('contract_id', $context->payload['contract_id'])
            ],
            'limit_type' => [
                'required',
                new Enum(LimitType::class)
            ]
        ];
    }
}
