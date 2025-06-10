<?php

declare(strict_types=1);

namespace App\UseCases\Contract\Delete;

use App\ReadModels\ContractFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;

readonly class DeleteHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private SpecificationInterface $specification, private ContractFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(Command $command): void
    {
        /** @var DeleteCommand $command */

        $contract = $this->fetcher->getOne($command->id);

        $this->specification->throwExceptionIfIsNotSatisfiedBy($contract);

        $contract->delete();
    }
}
