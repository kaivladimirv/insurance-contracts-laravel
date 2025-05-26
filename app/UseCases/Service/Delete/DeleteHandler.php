<?php

declare(strict_types=1);

namespace App\UseCases\Service\Delete;

use App\ReadModels\ServiceFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;

readonly class DeleteHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private SpecificationInterface $specification, private ServiceFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(DeleteCommand|Command $command): void
    {
        $service = $this->fetcher->getOne($command->company_id, $command->id);

        $this->specification->throwExceptionIfIsNotSatisfiedBy($service);

        $service->delete();
    }
}
