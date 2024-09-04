<?php

declare(strict_types=1);

namespace App\UseCases\Contract\Add;

use App\Models\Contract;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class AddHandler implements CommandHandler
{
    public function handle(AddCommand|Command $command): int
    {
        $contract = new Contract();
        $contract->fill($command->only(...$contract->getFillable())->toArray());
        $contract->company()->associate($command->company_id);
        $contract->save();

        return $contract->id;
    }
}
