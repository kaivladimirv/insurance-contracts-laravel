<?php

declare(strict_types=1);

namespace App\UseCases\Contract\Update;

use App\ReadModels\ContractFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class UpdateHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private ContractFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(UpdateCommand|Command $command): void
    {
        $contract = $this->fetcher->getOne($command->id);
        $contract->fill($this->extractFillableData($command, $contract));
        $contract->save();
    }
}
