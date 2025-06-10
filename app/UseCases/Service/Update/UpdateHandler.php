<?php

declare(strict_types=1);

namespace App\UseCases\Service\Update;

use App\ReadModels\ServiceFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class UpdateHandler extends AbstractHandler
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
        $service->fill($this->extractFillableData($command, $service));
        $service->save();
    }
}
