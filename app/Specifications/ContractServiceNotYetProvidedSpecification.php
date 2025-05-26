<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\InUse;
use App\Models\ContractService;
use App\ReadModels\ProvidedServiceFetcher;
use Kaivladimirv\LaravelSpecificationPattern\AbstractSpecification;
use Override;

class ContractServiceNotYetProvidedSpecification extends AbstractSpecification
{
    /**
     * @psalm-api
     */
    public function __construct(private readonly ProvidedServiceFetcher $providedServiceFetcher)
    {
    }

    #[Override]
    protected function defineMessage(): string
    {
        return __('The limit type cannot be changed because service already provided');
    }

    /**
     * @param ContractService $candidate
     */
    #[Override]
    protected function executeCheckIsSatisfiedBy(mixed $candidate): bool
    {
        return $this->providedServiceFetcher->isServiceProvidedInContract($candidate->contract_id, $candidate->service_id) === false;
    }

    #[Override]
    protected function getExceptionClass(): string
    {
        return InUse::class;
    }
}
