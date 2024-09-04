<?php

declare(strict_types=1);

namespace App\UseCases\Contract\Update;

use App\Models\Contract;
use App\UseCases\Command;
use DateTimeImmutable;
use Illuminate\Validation\Rule;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\AfterOrEqual;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

#[Schema(
    schema: 'ContractUpdateCommand',
    title: 'ContractUpdateCommand',
    required: ['number', 'start_date', 'end_date', 'max_amount'],
    properties: [
        new Property(
            property: 'number',
            description: 'Contract number',
            type: 'string',
            maxLength: 50,
            example: 'DK-12-01'
        ),
        new Property(
            property: 'start_date',
            description: 'Contract start date',
            type: 'string',
            format: 'date',
            example: '2024-01-01'
        ),
        new Property(
            property: 'end_date',
            description: 'Contract end date',
            type: 'string',
            format: 'date',
            example: '2024-12-31'
        ),
        new Property(
            property: 'max_amount',
            description: 'Max amount',
            type: 'number',
            minimum: 0,
            example: 1234516
        )
    ]
)]
class UpdateCommand extends Data implements Command
{
    public function __construct(
        readonly public int $company_id,
        #[FromRouteParameter('id')]
        readonly public int $id,
        readonly public string $number,
        #[Date]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        readonly public DateTimeImmutable $start_date,
        #[Date]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        #[AfterOrEqual('start_date')]
        readonly public DateTimeImmutable $end_date,
        #[Numeric]
        #[Min(0)]
        readonly public int|float $max_amount
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'number' => [
                'required',
                'max:50',
                Rule::unique(Contract::class, 'number')->where('company_id', $context->payload['company_id'])->ignore($context->payload['id'])
            ]
        ];
    }
}
