<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\ProvidedService\ServiceIsNotCovered;
use App\Models\ProvidedService;
use App\ReadModels\ContractServiceFetcher;
use Kaivladimirv\LaravelSpecificationPattern\AbstractSpecification;
use Override;

class ServiceIsCoveredSpecification extends AbstractSpecification
{
    /**
     * @psalm-api
     */
    public function __construct(private readonly ContractServiceFetcher $contractServiceFetcher)
    {
    }

    #[Override]
    protected function defineMessage(): string
    {
        return __('The service is not covered by insurance');
    }

    #[Override]
    protected function executeCheckIsSatisfiedBy(mixed $candidate): bool
    {
        /** @var ProvidedService $candidate */

        return $this->contractServiceFetcher->isExist($candidate->contract_id, $candidate->service_id);
    }

    #[Override]
    protected function getExceptionClass(): string
    {
        return ServiceIsNotCovered::class;
    }
}
