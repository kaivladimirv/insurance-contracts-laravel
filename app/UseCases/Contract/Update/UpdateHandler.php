<?php

declare(strict_types=1);

namespace App\UseCases\Contract\Update;

use App\ReadModels\ContractFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class UpdateHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private ContractFetcher $fetcher)
    {
    }

    public function handle(UpdateCommand|Command $command): void
    {
        $contract = $this->fetcher->getOne($command->company_id, $command->id);
        $contract->fill($command->only(...$contract->getFillable())->toArray());
        $contract->save();
    }
}
