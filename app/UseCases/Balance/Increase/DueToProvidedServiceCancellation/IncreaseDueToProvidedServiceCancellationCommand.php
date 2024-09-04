<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Increase\DueToProvidedServiceCancellation;

use App\UseCases\Command;

readonly class IncreaseDueToProvidedServiceCancellationCommand implements Command
{
    public function __construct(public int $providedServiceId)
    {
    }
}
