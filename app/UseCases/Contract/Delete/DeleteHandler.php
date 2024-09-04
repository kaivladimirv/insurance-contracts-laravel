<?php

declare(strict_types=1);

namespace App\UseCases\Contract\Delete;

use App\Exceptions\InUse;
use App\ReadModels\ContractFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class DeleteHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private ContractFetcher $fetcher)
    {
    }

    /**
     * @throws InUse
     */
    public function handle(DeleteCommand|Command $command): void
    {
        $contract = $this->fetcher->getOne($command->company_id, $command->id);

        if ($contract->providedServices()->select('id')->exists()) {
            throw new InUse(__('Services were provided under the contract'));
        }

        $contract->delete();
    }
}
