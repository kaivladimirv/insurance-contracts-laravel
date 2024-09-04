<?php

declare(strict_types=1);

namespace App\Jobs\Balance;

use App\UseCases\Balance\Remove\RemoveBalancesForServiceCommand;
use App\UseCases\Balance\Remove\RemoveBalancesForServiceHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemoveBalancesForService implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $contractId,
        private readonly int $serviceId
    ) {
        $this->onQueue('balances');
    }

    public function handle(RemoveBalancesForServiceHandler $handler): void
    {
        $command = new RemoveBalancesForServiceCommand($this->contractId, $this->serviceId);
        $handler->handle($command);
    }
}
