<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\InsuredPerson\InsuredPersonAdded;
use App\Jobs\Balance\RecalcBalancesForInsured;

class InsuredPersonEventSubscriber
{
    public function handleAdded(InsuredPersonAdded $event): void
    {
        RecalcBalancesForInsured::dispatch($event->insuredPersonId);
    }

    public function subscribe(): array
    {
        return [
            InsuredPersonAdded::class => 'handleAdded'
        ];
    }
}
