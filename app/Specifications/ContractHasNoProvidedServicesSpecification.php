<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\InUse;
use App\Models\Contract;
use Kaivladimirv\LaravelSpecificationPattern\AbstractSpecification;
use Override;

class ContractHasNoProvidedServicesSpecification extends AbstractSpecification
{
    #[Override]
    protected function defineMessage(): string
    {
        return __('Services were provided under the contract');
    }

    /**
     * @param Contract $candidate
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
