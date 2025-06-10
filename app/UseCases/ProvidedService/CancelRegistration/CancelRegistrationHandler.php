<?php

declare(strict_types=1);

namespace App\UseCases\ProvidedService\CancelRegistration;

use App\Events\ProvidedService\RegistrationOfProvidedServiceCanceled;
use App\ReadModels\ProvidedServiceFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class CancelRegistrationHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private ProvidedServiceFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(Command $command): void
    {
        /** @var CancelRegistrationCommand $command */

        $providedService = $this->fetcher->getOne($command->insured_person_id, $command->id);

        $providedService->delete();

        RegistrationOfProvidedServiceCanceled::dispatch($providedService->toDto());
    }
}
