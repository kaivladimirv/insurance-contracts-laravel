<?php

declare(strict_types=1);

namespace App\Jobs\Balance;

use App\UseCases\Balance\Recalc\ByServiceAndInsuredPerson\RecalcBalanceByServiceAndInsuredPersonCommand;
use App\UseCases\Balance\Recalc\ByServiceAndInsuredPerson\RecalcBalanceByServiceAndInsuredPersonHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecalcBalance implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $contractId,
        private readonly int $insuredPersonId,
        private readonly int $serviceId
    ) {
        $this->onQueue('balances');
    }

    public function handle(RecalcBalanceByServiceAndInsuredPersonHandler $handler): void
    {
        $command = new RecalcBalanceByServiceAndInsuredPersonCommand($this->contractId, $this->serviceId, $this->insuredPersonId);
        $handler->handle($command);
    }
}
