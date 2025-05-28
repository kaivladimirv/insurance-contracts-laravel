<?php

declare(strict_types=1);

namespace App\UseCases\Contract\Delete;

use App\ReadModels\ContractFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;

readonly class DeleteHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private SpecificationInterface $specification, private ContractFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(DeleteCommand|Command $command): void
    {
        $contract = $this->fetcher->getOne($command->id);

        $this->specification->throwExceptionIfIsNotSatisfiedBy($contract);

        $contract->delete();
    }
}
