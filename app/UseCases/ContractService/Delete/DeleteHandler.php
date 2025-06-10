<?php

declare(strict_types=1);

namespace App\UseCases\ContractService\Delete;

use App\Events\ContractService\RemoveServiceFromContract;
use App\ReadModels\ContractServiceFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class DeleteHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private ContractServiceFetcher $contractServiceFetcher)
    {
    }

    #[Override]
    public function handle(DeleteCommand|Command $command): void
    {
        $contractService = $this->contractServiceFetcher->getOne($command->contract_id, $command->service_id);
        $contractService->delete();

        RemoveServiceFromContract::dispatch($command->contract_id, $command->service_id);
    }
}
