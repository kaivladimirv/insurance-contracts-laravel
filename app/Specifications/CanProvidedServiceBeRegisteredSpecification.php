<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\ProvidedService\RegistrationOfProvidedService;
use Kaivladimirv\LaravelSpecificationPattern\AndSpecification;
use Override;

class CanProvidedServiceBeRegisteredSpecification extends AndSpecification
{
    #[Override]
    protected function getExceptionClass(): string
    {
        return RegistrationOfProvidedService::class;
    }
}
