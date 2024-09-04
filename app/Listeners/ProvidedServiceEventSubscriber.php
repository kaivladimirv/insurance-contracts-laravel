<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ProvidedService\ProvidedServiceRegistered;
use App\Events\ProvidedService\RegistrationOfProvidedServiceCanceled;
use App\UseCases\Balance\Decrease\DueToProvidedService\DecreaseDueToProvidedServiceCommand;
use App\UseCases\Balance\Decrease\DueToProvidedService\DecreaseDueToProvidedServiceHandler;
use App\UseCases\Balance\Increase\DueToProvidedServiceCancellation\IncreaseDueToProvidedServiceCancellationCommand;
use App\UseCases\Balance\Increase\DueToProvidedServiceCancellation\IncreaseDueToProvidedServiceCancellationHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;

class ProvidedServiceEventSubscriber implements ShouldQueue
{
    public function handleRegistered(ProvidedServiceRegistered $event): void
    {
        $command = new DecreaseDueToProvidedServiceCommand($event->providedServiceId);

        /** @var DecreaseDueToProvidedServiceHandler $handler */
        $handler = App::make(DecreaseDueToProvidedServiceHandler::class);
        $handler->handle($command);
    }

    public function handleCanceled(RegistrationOfProvidedServiceCanceled $event): void
    {
        $command = new IncreaseDueToProvidedServiceCancellationCommand($event->providedServiceId);

        /** @var IncreaseDueToProvidedServiceCancellationHandler $handler */
        $handler = App::make(IncreaseDueToProvidedServiceCancellationHandler::class);
        $handler->handle($command);
    }

    public function subscribe(): array
    {
        return [
            ProvidedServiceRegistered::class => 'handleRegistered',
            RegistrationOfProvidedServiceCanceled::class => 'handleCanceled'
        ];
    }
}
