<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Recalc\ByInsured;

use App\Exceptions\Balance\RecalculationOfBalance;
use App\Services\RecalcBalancesForInsured;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Override;

class RecalcBalanceByInsuredHandler implements CommandHandler
{
    public function __construct(private readonly RecalcBalancesForInsured $recalculator)
    {
    }

    /**
     * @throws RecalculationOfBalance
     */
    #[Override]
    public function handle(Command $command): void
    {
        /** @var RecalcBalanceByInsuredCommand $command*/

        $this->recalculator->recalc($command->insured_person_id);
    }
}
