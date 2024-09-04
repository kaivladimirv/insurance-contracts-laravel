<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Remove;

use App\Models\Balance;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class RemoveBalancesForServiceHandler implements CommandHandler
{
    public function handle(RemoveBalancesForServiceCommand|Command $command): void
    {
        Balance::byContractAndService($command->contract_id, $command->service_id)->delete();
    }
}
