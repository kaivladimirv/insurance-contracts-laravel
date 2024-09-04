<?php

declare(strict_types=1);

namespace App\Events\ContractService;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemoveServiceFromContract implements ShouldDispatchAfterCommit
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        readonly public int $contractId,
        readonly public int $serviceId
    ) {
    }
}
