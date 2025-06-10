<?php

declare(strict_types=1);

namespace App\UseCases\Contract\Add;

use App\Models\Contract;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class AddHandler extends AbstractHandler
{
    #[Override]
    public function handle(AddCommand|Command $command): int
    {
        $contract = new Contract();
        $contract->fill($this->extractFillableData($command, $contract));
        $contract->company()->associate($command->company_id);
        $contract->save();

        return $contract->id;
    }
}
