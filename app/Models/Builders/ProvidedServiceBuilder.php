<?php

declare(strict_types=1);

namespace App\Models\Builders;

use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use App\ReadModels\ContractServiceFetcher;
use App\UseCases\ProvidedService\Registration\RegistrationCommand;

readonly class ProvidedServiceBuilder
{
    /**
     * @psalm-api
     */
    public function __construct(private ContractServiceFetcher $contractServiceFetcher)
    {
    }

    public function buildFromCommand(RegistrationCommand $command): ProvidedService
    {
        /** @var InsuredPerson $insuredPerson */
        $insuredPerson = InsuredPerson::query()->findOrFail($command->insured_person_id);
        $contractService = $this->contractServiceFetcher->getOne($insuredPerson->contract->id, $command->service_id);

        $providedService = new ProvidedService();
        $providedService->fill($command->only(...$providedService->getFillable())->toArray());
        $providedService->service_name = $contractService->service->name;
        $providedService->limit_type = $contractService->limit_type;
        $providedService->recalcAmount();

        $providedService->company()->associate($contractService->contract->company);
        $providedService->contract()->associate($insuredPerson->contract);
        $providedService->insuredPerson()->associate($insuredPerson);

        return $providedService;
    }
}
