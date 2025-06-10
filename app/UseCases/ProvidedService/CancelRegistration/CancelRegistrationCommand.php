<?php

declare(strict_types=1);

namespace App\UseCases\ProvidedService\CancelRegistration;

use App\UseCases\Command;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;

class CancelRegistrationCommand extends Data implements Command
{
    public function __construct(
        #[FromRouteParameter('insured_person_id')]
        readonly public int $insured_person_id,
        #[FromRouteParameter('provided_service_id')]
        readonly public int $id
    ) {
    }
}
