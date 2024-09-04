<?php

declare(strict_types=1);

namespace App\UseCases\ContractService\Update;

use App\Enums\LimitType;
use App\UseCases\Command;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Data;

#[Schema(
    schema: 'ContractServiceUpdateCommand',
    title: 'ContractServiceUpdateCommand',
    required: ['limit_type', 'limit_value'],
    properties: [
        new Property(
            property: 'limit_type',
            description: 'Limit type',
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
class UpdateCommand extends Data implements Command
{
    public function __construct(
        #[FromRouteParameter('contract_id')]
        readonly public int $contract_id,
        #[FromRouteParameter('service_id')]
        readonly public int $service_id,
        readonly public LimitType $limit_type,
        #[Numeric]
        #[Min(0)]
        readonly public int|float $limit_value
    ) {
    }

    public static function rules(): array
    {
        return [
            'limit_type' => [
                'required',
                new Enum(LimitType::class)
            ]
        ];
    }
}
