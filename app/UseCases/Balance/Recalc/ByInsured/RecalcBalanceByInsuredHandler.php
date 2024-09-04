<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Recalc\ByInsured;

use App\Exceptions\Balance\RecalculationOfBalance;
use App\Services\RecalcBalancesForInsured;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

class RecalcBalanceByInsuredHandler implements CommandHandler
{
    public function __construct(private readonly RecalcBalancesForInsured $recalculator)
    {
    }

    /**
     * @throws RecalculationOfBalance
     */
    public function handle(RecalcBalanceByInsuredCommand|Command $command): void
    {
        $this->recalculator->recalc($command->insured_person_id);
    }
}
