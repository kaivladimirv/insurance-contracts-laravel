<?php

declare(strict_types=1);

namespace App\UseCases\Person\Delete;

use App\ReadModels\PersonFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;

readonly class DeleteHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private SpecificationInterface $specification, private PersonFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(Command $command): void
    {
        /** @var DeleteCommand $command */

        $person = $this->fetcher->getOne($command->id);

        $this->specification->throwExceptionIfIsNotSatisfiedBy($person);

        $person->delete();
    }
}
