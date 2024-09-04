<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Decrease\DueToProvidedService;

use App\UseCases\Command;

readonly class DecreaseDueToProvidedServiceCommand implements Command
{
    public function __construct(public int $providedServiceId)
    {
    }
}
