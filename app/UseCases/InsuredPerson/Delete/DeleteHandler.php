<?php

declare(strict_types=1);

namespace App\UseCases\InsuredPerson\Delete;

use App\ReadModels\InsuredPersonFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;

readonly class DeleteHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private SpecificationInterface $specification, private InsuredPersonFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(DeleteCommand|Command $command): void
    {
        $insuredPerson = $this->fetcher->getOne($command->contract_id, $command->insured_person_id);

        $this->specification->throwExceptionIfIsNotSatisfiedBy($insuredPerson);

        $insuredPerson->delete();
    }
}
