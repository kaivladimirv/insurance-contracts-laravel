<?php

declare(strict_types=1);

namespace App\UseCases\ContractService\Add;

use App\Events\ContractService\ServiceAddedToContract;
use App\Models\ContractService;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class AddHandler extends AbstractHandler
{
    #[Override]
    public function handle(AddCommand|Command $command): void
    {
        $contractService = new ContractService();
        $contractService->fill($this->extractFillableData($command, $contractService));
        $contractService->contract()->associate($command->contract_id);
        $contractService->save();

        ServiceAddedToContract::dispatch($command->contract_id, $command->service_id);
    }
}
