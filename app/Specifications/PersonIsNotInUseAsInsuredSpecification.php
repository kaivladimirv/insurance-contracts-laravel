<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\InUse;
use App\Models\Person;
use Kaivladimirv\LaravelSpecificationPattern\AbstractSpecification;
use Override;

class PersonIsNotInUseAsInsuredSpecification extends AbstractSpecification
{
    #[Override]
    protected function defineMessage(): string
    {
        return __('The person is the insured person');
    }

    /**
     * @param Person $candidate
     */
    #[Override]
    protected function executeCheckIsSatisfiedBy(mixed $candidate): bool
    {
        return $candidate->insuredPersons()->select('id')->exists() === false;
    }

    #[Override]
    protected function getExceptionClass(): string
    {
        return InUse::class;
    }
}
