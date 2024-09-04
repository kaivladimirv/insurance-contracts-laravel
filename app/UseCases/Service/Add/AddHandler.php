<?php

declare(strict_types=1);

namespace App\UseCases\Service\Add;

use App\Models\Service;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class AddHandler implements CommandHandler
{
    public function handle(AddCommand|Command $command): int
    {
        $service = new Service();
        $service->fill($command->only(...$service->getFillable())->toArray());
        $service->company()->associate($command->company_id);
        $service->save();

        return $service->id;
    }
}
