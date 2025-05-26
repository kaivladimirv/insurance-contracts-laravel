<?php

declare(strict_types=1);

namespace App\UseCases\Person\Delete;

use App\ReadModels\PersonFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;

readonly class DeleteHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private SpecificationInterface $specification, private PersonFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(DeleteCommand|Command $command): void
    {
        $person = $this->fetcher->getOne($command->company_id, $command->id);

        $this->specification->throwExceptionIfIsNotSatisfiedBy($person);

        $person->delete();
    }
}
