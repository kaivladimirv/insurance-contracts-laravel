<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Recalc\ByService;

use App\Exceptions\Balance\RecalculationOfBalance;
use App\Services\RecalcBalancesForService;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class RecalcBalanceByServiceHandler implements CommandHandler
{
    public function __construct(private RecalcBalancesForService $recalculator)
    {
    }

    /**
     * @throws RecalculationOfBalance
     */
    public function handle(RecalcBalanceByServiceCommand|Command $command): void
    {
        $this->recalculator->recalc($command->contract_id, $command->service_id);
    }
}
