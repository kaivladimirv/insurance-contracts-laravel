<?php

declare(strict_types=1);

namespace App\Jobs\Balance;

use App\Exceptions\Balance\RecalculationOfBalance;
use App\UseCases\Balance\Recalc\ByInsured\RecalcBalanceByInsuredCommand;
use App\UseCases\Balance\Recalc\ByInsured\RecalcBalanceByInsuredHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecalcBalancesForInsured implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $insuredPersonId
    ) {
        $this->onQueue('balances');
    }

    /**
     * @throws RecalculationOfBalance
     */
    public function handle(RecalcBalanceByInsuredHandler $handler): void
    {
        $command = new RecalcBalanceByInsuredCommand($this->insuredPersonId);
        $handler->handle($command);
    }
}
