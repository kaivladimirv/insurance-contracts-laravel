<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Decrease\DueToProvidedService;

use App\Dto\ProvidedServiceDto;
use App\UseCases\Command;

readonly class DecreaseDueToProvidedServiceCommand implements Command
{
    public function __construct(public ProvidedServiceDto $providedServiceDto)
    {
    }
}
