<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\InUse;
use App\Models\Service;
use Kaivladimirv\LaravelSpecificationPattern\AbstractSpecification;
use Override;

class ServiceIsNotUsedInContractsSpecification extends AbstractSpecification
{
    #[Override]
    protected function defineMessage(): string
    {
        return __('The service is used in contracts');
    }

    /**
     * @param Service $candidate
     */
    #[Override]
    protected function executeCheckIsSatisfiedBy(mixed $candidate): bool
    {
        return $candidate->contractServices()->select('id')->exists() === false;
    }

    #[Override]
    protected function getExceptionClass(): string
    {
        return InUse::class;
    }
}
