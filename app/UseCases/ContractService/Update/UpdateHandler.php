<?php

declare(strict_types=1);

namespace App\UseCases\ContractService\Update;

use App\Events\ContractService\ServiceUpdatedToContract;
use App\ReadModels\ContractServiceFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;

readonly class UpdateHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private SpecificationInterface $specification, private ContractServiceFetcher $contractServiceFetcher)
    {
    }

    #[Override]
    public function handle(UpdateCommand|Command $command): void
    {
        $contractService = $this->contractServiceFetcher->getOne($command->contract_id, $command->service_id);

        if ($contractService->limit_type !== $command->limit_type) {
            $this->specification->throwExceptionIfIsNotSatisfiedBy($contractService);
        }

        $contractService->fill($command->only('limit_type', 'limit_value')->all());

        if (!$contractService->isClean()) {
            $contractService->save();

            ServiceUpdatedToContract::dispatch($command->contract_id, $command->service_id);
        }
    }
}
