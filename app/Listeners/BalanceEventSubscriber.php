<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Balance\BalanceWasDecreasedDueToProvidedService;
use App\Notifications\Balance\BalanceDecreasedDueToProvidedService;
use App\ReadModels\PersonFetcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;

class BalanceEventSubscriber implements ShouldQueue
{
    public function handleBalanceWasDecreasedDueToProvidedService(BalanceWasDecreasedDueToProvidedService $event): void
    {
        /** @var PersonFetcher $personFetcher */
        $personFetcher = App::make(PersonFetcher::class);
        $person = $personFetcher->getOneByInsuredPersonId($event->balance->insured_person_id);

        $person->notify(new BalanceDecreasedDueToProvidedService($event->balance, $event->providedService));
    }

    public function subscribe(): array
    {
        return [
            BalanceWasDecreasedDueToProvidedService::class => 'handleBalanceWasDecreasedDueToProvidedService'
        ];
    }
}
