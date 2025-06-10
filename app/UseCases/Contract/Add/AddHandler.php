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
    public function handle(Command $command): int
    {
        /** @var AddCommand $command */

        $contract = new Contract();
        $contract->fill($this->extractFillableData($command, $contract));
        $contract->company()->associate($command->company_id);
        $contract->save();

        return $contract->id;
    }
}
