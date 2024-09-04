<?php

declare(strict_types=1);

namespace App\UseCases\Service\Update;

use App\Models\Service;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class UpdateHandler implements CommandHandler
{
    public function handle(UpdateCommand|Command $command): void
    {

        $service = Service::query()->findOrFail($command->id);
        $service->fill($command->only(...$service->getFillable())->toArray());
        $service->save();
    }
}
