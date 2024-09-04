<?php

declare(strict_types=1);

namespace App\Events\ProvidedService;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

readonly class RegistrationOfProvidedServiceCanceled implements ShouldDispatchAfterCommit
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public int $providedServiceId)
    {
    }
}
