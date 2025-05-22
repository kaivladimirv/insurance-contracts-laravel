<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Balance\BalanceWasDecreasedDueToProvidedService;
use App\Events\Balance\BalanceWasIncreasedDueToProvidedService;
use App\Models\Person;
use App\Notifications\Balance\BalanceDecreasedDueToProvidedService;
use App\Notifications\Balance\BalanceIncreasedDueToProvidedServiceCancellation;
use App\ReadModels\PersonFetcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;

class BalanceEventSubscriber implements ShouldQueue
{
    public function handleBalanceWasDecreasedDueToProvidedService(BalanceWasDecreasedDueToProvidedService $event): void
    {
        $person = $this->getPersonByInsuredPersonId($event->providedServiceDto->insuredPersonId);

        $person->notify(new BalanceDecreasedDueToProvidedService($event->balance, $event->providedServiceDto));
    }

    public function handleBalanceWasIncreasedDueToProvidedService(BalanceWasIncreasedDueToProvidedService $event): void
    {
        $person = $this->getPersonByInsuredPersonId($event->providedServiceDto->insuredPersonId);

        $person->notify(new BalanceIncreasedDueToProvidedServiceCancellation($event->balance, $event->providedServiceDto));
    }

    private function getPersonByInsuredPersonId(int $insuredPersonId): Person
    {
        /** @var PersonFetcher $personFetcher */
        $personFetcher = App::make(PersonFetcher::class);

        return $personFetcher->getOneByInsuredPersonId($insuredPersonId);
    }

    public function subscribe(): array
    {
        return [
            BalanceWasDecreasedDueToProvidedService::class => 'handleBalanceWasDecreasedDueToProvidedService',
            BalanceWasIncreasedDueToProvidedService::class => 'handleBalanceWasIncreasedDueToProvidedService'
        ];
    }
}
