<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\ProvidedService\AmountOfServicesProvidedExceedsMaxAmountUnderContract;
use App\Models\ProvidedService;
use App\ReadModels\ProvidedServiceFetcher;
use Kaivladimirv\LaravelSpecificationPattern\AbstractSpecification;
use Override;

class MaxAmountUnderContractIsNotExceededSpecification extends AbstractSpecification
{
    /**
     * @psalm-api
     */
    public function __construct(private readonly ProvidedServiceFetcher $fetcher)
    {
    }

    #[Override]
    protected function defineMessage(): string
    {
        return '';
    }

    /**
     * @param ProvidedService $candidate
     */
    #[Override]
    protected function executeCheckIsSatisfiedBy(mixed $candidate): bool
    {
        $amountOfProvidedServices = $this->fetcher->getAmountByInsuredPersonId($candidate->insured_person_id);

        if (($amountOfProvidedServices + $candidate->amount) <= $candidate->contract->max_amount) {
            return true;
        }

        $replace = [
            'maxAmount' => $candidate->contract->max_amount,
            'remainder' => $candidate->contract->max_amount - $amountOfProvidedServices
        ];

        $this->setMessage(__('The amount of services provided exceeds the maximum amount under the contract', $replace));

        return false;
    }

    #[Override]
    protected function getExceptionClass(): string
    {
        return AmountOfServicesProvidedExceedsMaxAmountUnderContract::class;
    }
}
