<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Recalc\ByService;

use App\UseCases\Command;

readonly class RecalcBalanceByServiceCommand implements Command
{
    public function __construct(
        public int $contract_id,
        public int $service_id
    ) {
    }
}
