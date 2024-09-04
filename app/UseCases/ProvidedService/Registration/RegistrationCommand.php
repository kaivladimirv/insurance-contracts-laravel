<?php

declare(strict_types=1);

namespace App\UseCases\ProvidedService\Registration;

use App\UseCases\Command;
use DateTimeImmutable;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\GreaterThan;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

#[Schema(
    schema: 'ProvidedServiceRegistrationCommand',
    title: 'ProvidedServiceRegistrationCommand',
    required: ['service_id', 'date_of_service', 'quantity', 'price'],
    properties: [
        new Property(
            property: 'service_id',
            description: 'Service id',
            type: 'integer',
            example: 1
        ),
        new Property(
            property: 'date_of_service',
            description: 'Date of service',
            type: 'string',
            format: 'date',
            example: '2024-01-12'
        ),
        new Property(
            property: 'quantity',
            description: 'Quantity',
            type: 'number',
            example: 1,
        ),
        new Property(
            property: 'price',
            description: 'Price',
            type: 'number',
            example: 1500,
        )
    ]
)]
class RegistrationCommand extends Data implements Command
{
    public function __construct(
        #[FromRouteParameter('insured_person_id')]
        readonly public int $insured_person_id,
        readonly public int $service_id,
        #[Date]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        readonly public DateTimeImmutable $date_of_service,
        #[GreaterThan('0')]
        readonly public int|float $quantity,
        #[GreaterThan('0')]
        readonly public int|float $price
    ) {
    }
}
