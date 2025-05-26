<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\InUse;
use App\Models\InsuredPerson;
use Kaivladimirv\LaravelSpecificationPattern\AbstractSpecification;
use Override;

class InsuredPersonHasNoProvidedServicesSpecification extends AbstractSpecification
{
    #[Override]
    protected function defineMessage(): string
    {
        return __('Services have already been provided to the insured person');
    }

    /**
     * @param InsuredPerson $candidate
     */
    #[Override]
    protected function executeCheckIsSatisfiedBy(mixed $candidate): bool
    {
        return $candidate->providedServices()->select('id')->exists() === false;
    }

    #[Override]
    protected function getExceptionClass(): string
    {
        return InUse::class;
    }
}
