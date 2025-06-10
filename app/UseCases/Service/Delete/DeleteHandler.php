<?php

declare(strict_types=1);

namespace App\UseCases\Service\Delete;

use App\ReadModels\ServiceFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;

readonly class DeleteHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private SpecificationInterface $specification, private ServiceFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(Command $command): void
    {
        /** @var DeleteCommand $command */

        $service = $this->fetcher->getOne($command->id);

        $this->specification->throwExceptionIfIsNotSatisfiedBy($service);

        $service->delete();
    }
}
