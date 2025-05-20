<?php

declare(strict_types=1);

namespace App\Events\Balance;

use App\Models\Balance;
use App\Models\ProvidedService;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\SerializesModels;

readonly class BalanceWasDecreasedDueToProvidedService
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        #[WithoutRelations]
        public Balance $balance,
        #[WithoutRelations]
        public ProvidedService $providedService
    ) {
    }
}
