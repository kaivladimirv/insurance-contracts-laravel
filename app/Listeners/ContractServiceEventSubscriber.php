<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ContractService\RemoveServiceFromContract;
use App\Events\ContractService\ServiceAddedToContract;
use App\Events\ContractService\ServiceUpdatedToContract;
use App\Jobs\Balance\RecalcBalancesForService;
use App\Jobs\Balance\RemoveBalancesForService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContractServiceEventSubscriber implements ShouldQueue
{
    public function handleServiceAdded(ServiceAddedToContract $event): void
    {
        RecalcBalancesForService::dispatch($event->contractId, $event->serviceId);
    }

    public function handleServiceUpdated(ServiceUpdatedToContract $event): void
    {
        RecalcBalancesForService::dispatch($event->contractId, $event->serviceId);
    }

    public function handleServiceDeleted(RemoveServiceFromContract $event): void
    {
        RemoveBalancesForService::dispatch($event->contractId, $event->serviceId);
    }

    public function subscribe(): array
    {
        return [
            ServiceAddedToContract::class => 'handleServiceAdded',
            ServiceUpdatedToContract::class => 'handleServiceUpdated',
            RemoveServiceFromContract::class => 'handleServiceDeleted'
        ];
    }
}
