<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\ProvidedService\DateOfServiceProvisionIsNotIncludedInTheContractPeriod;
use App\Models\ProvidedService;
use Exception;
use Kaivladimirv\LaravelSpecificationPattern\AbstractSpecification;
use Override;

class DateOfServiceIsIncludedInContractPeriodSpecification extends AbstractSpecification
{
    #[Override]
    protected function defineMessage(): string
    {
        return __('The date of service provision is not included in the contract period');
    }

    /**
     * @param ProvidedService $candidate
     * @throws Exception
     */
    #[Override]
    protected function executeCheckIsSatisfiedBy(mixed $candidate): bool
    {
        return ($candidate->contract->isIncludeInValidityPeriod($candidate->date_of_service->toDateTimeImmutable()) === true);
    }

    #[Override]
    protected function getExceptionClass(): string
    {
        return DateOfServiceProvisionIsNotIncludedInTheContractPeriod::class;
    }
}
