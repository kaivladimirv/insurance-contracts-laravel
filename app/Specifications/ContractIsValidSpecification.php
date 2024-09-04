<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\ProvidedService\ContractHasExpired;
use App\Models\ProvidedService;
use DateTimeImmutable;
use Kaivladimirv\LaravelSpecificationPattern\AbstractSpecification;
use Override;

class ContractIsValidSpecification extends AbstractSpecification
{
    public function __construct(readonly private DateTimeImmutable $date)
    {
    }

    #[Override]
    protected function defineMessage(): string
    {
        return __('The contract has expired');
    }

    /**
     * @param ProvidedService $candidate
     */
    #[Override]
    protected function executeCheckIsSatisfiedBy(mixed $candidate): bool
    {
        return $candidate->contract->isExpiredTo($this->date) === false;
    }

    #[Override]
    protected function getExceptionClass(): string
    {
        return ContractHasExpired::class;
    }
}
