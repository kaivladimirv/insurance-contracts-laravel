<?php

declare(strict_types=1);

namespace App\Events\Balance;

use App\Dto\ProvidedServiceDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

readonly class BalanceWasDecreasedDueToProvidedService
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public float $balance,
        public ProvidedServiceDto $providedServiceDto
    ) {
    }
}
