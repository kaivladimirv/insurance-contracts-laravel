<?php

declare(strict_types=1);

namespace App\UseCases\ContractService\Add;

use App\Events\ContractService\ServiceAddedToContract;
use App\Models\ContractService;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class AddHandler implements CommandHandler
{
    public function handle(AddCommand|Command $command): void
    {
        $contractService = new ContractService();
        $contractService->fill($command->only(...$contractService->getFillable())->all());
        $contractService->contract()->associate($command->contract_id);
        $contractService->save();

        ServiceAddedToContract::dispatch($command->contract_id, $command->service_id);
    }
}
