<?php

declare(strict_types=1);

namespace App\UseCases\ProvidedService\CancelRegistration;

use App\Events\ProvidedService\RegistrationOfProvidedServiceCanceled;
use App\ReadModels\ProvidedServiceFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class CancelRegistrationHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private ProvidedServiceFetcher $fetcher)
    {
    }

    public function handle(CancelRegistrationCommand|Command $command): void
    {
        $providedService = $this->fetcher->getOne($command->insured_person_id, $command->id);

        $providedService->delete();

        RegistrationOfProvidedServiceCanceled::dispatch($providedService->id);
    }
}
