<?php

declare(strict_types=1);

namespace App\UseCases\Service\Delete;

use App\Exceptions\InUse;
use App\ReadModels\ServiceFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class DeleteHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private ServiceFetcher $fetcher)
    {
    }

    /**
     * @throws InUse
     */
    public function handle(DeleteCommand|Command $command): void
    {
        $service = $this->fetcher->getOne($command->company_id, $command->id);

        if ($service->contractServices()->select('id')->exists()) {
            throw new InUse(__('The service is used in contracts'));
        }

        $service->delete();
    }
}
