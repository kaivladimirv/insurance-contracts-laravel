<?php

declare(strict_types=1);

namespace App\UseCases\ContractService\Update;

use App\Events\ContractService\ServiceUpdatedToContract;
use App\Exceptions\InUse;
use App\ReadModels\ContractServiceFetcher;
use App\ReadModels\ProvidedServiceFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class UpdateHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private ContractServiceFetcher $contractServiceFetcher, private ProvidedServiceFetcher $providedServiceFetcher)
    {
    }

    /**
     * @throws InUse
     */
    public function handle(UpdateCommand|Command $command): void
    {
        $contractService = $this->contractServiceFetcher->getOne($command->contract_id, $command->service_id);

        if ($contractService->limit_type !== $command->limit_type) {
            $this->assertServiceWasNotProvided($command);
        }

        $contractService->fill($command->only('limit_type', 'limit_value')->all());

        if (!$contractService->isClean()) {
            $contractService->save();

            ServiceUpdatedToContract::dispatch($command->contract_id, $command->service_id);
        }
    }

    /**
     * @throws InUse
     */
    private function assertServiceWasNotProvided(UpdateCommand $command): void
    {
        if ($this->providedServiceFetcher->isServiceProvidedInContract($command->contract_id, $command->service_id)) {
            throw new InUse(__('The limit type cannot be changed because service already provided'));
        }
    }
}
