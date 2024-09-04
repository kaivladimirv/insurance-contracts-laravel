<?php

declare(strict_types=1);

namespace App\Jobs\Balance;

use App\ReadModels\InsuredPersonFetcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecalcBalancesForService implements ShouldQueue
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

    public function handle(InsuredPersonFetcher $fetcher): void
    {
        $fetcher->getAllIdsByContract($this->contractId)->each(
            fn(int $insuredPersonId) => RecalcBalance::dispatch(
                $this->contractId,
                $insuredPersonId,
                $this->serviceId
            )
        );
    }
}
