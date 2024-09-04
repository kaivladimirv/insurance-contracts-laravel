<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Remove;

use App\UseCases\Command;

readonly class RemoveBalancesForServiceCommand implements Command
{
    public function __construct(
        public int $contract_id,
        public int $service_id
    ) {
    }
}
