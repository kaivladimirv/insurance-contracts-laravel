<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Remove;

use App\Models\Balance;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Override;

readonly class RemoveBalancesForServiceHandler implements CommandHandler
{
    #[Override]
    public function handle(RemoveBalancesForServiceCommand|Command $command): void
    {
        Balance::forContractAndService($command->contract_id, $command->service_id)->delete();
    }
}
