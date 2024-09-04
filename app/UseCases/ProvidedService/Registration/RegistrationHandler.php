<?php

declare(strict_types=1);

namespace App\UseCases\ProvidedService\Registration;

use App\Events\ProvidedService\ProvidedServiceRegistered;
use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use App\ReadModels\ContractServiceFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;

readonly class RegistrationHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private SpecificationInterface $specification, private ContractServiceFetcher $contractServiceFetcher)
    {
    }

    public function handle(RegistrationCommand|Command $command): int
    {
        $providedService = $this->buildProvidedService($command);
        $this->specification->throwExceptionIfIsNotSatisfiedBy($providedService);
        $providedService->save();

        ProvidedServiceRegistered::dispatch($providedService->id);

        return $providedService->id;
    }

    private function buildProvidedService(RegistrationCommand $command): ProvidedService
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
