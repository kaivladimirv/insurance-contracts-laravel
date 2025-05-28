<?php

declare(strict_types=1);

namespace App\UseCases\Service\Update;

use App\ReadModels\ServiceFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Override;

readonly class UpdateHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private ServiceFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(UpdateCommand|Command $command): void
    {
        $service = $this->fetcher->getOne($command->id);
        $service->fill($command->only(...$service->getFillable())->toArray());
        $service->save();
    }
}
