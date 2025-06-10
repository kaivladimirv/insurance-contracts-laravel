<?php

declare(strict_types=1);

namespace App\UseCases\Service\Add;

use App\Models\Service;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class AddHandler extends AbstractHandler
{
    #[Override]
    public function handle(AddCommand|Command $command): int
    {
        $service = new Service();
        $service->fill($this->extractFillableData($command, $service));
        $service->company()->associate($command->company_id);
        $service->save();

        return $service->id;
    }
}
