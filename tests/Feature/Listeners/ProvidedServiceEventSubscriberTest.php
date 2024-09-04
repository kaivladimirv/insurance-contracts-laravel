<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\ProvidedService\ProvidedServiceRegistered;
use App\Events\ProvidedService\RegistrationOfProvidedServiceCanceled;
use App\Listeners\ProvidedServiceEventSubscriber;
use App\Models\ProvidedService;
use App\UseCases\Balance\Decrease\DueToProvidedService\DecreaseDueToProvidedServiceHandler;
use App\UseCases\Balance\Increase\DueToProvidedServiceCancellation\IncreaseDueToProvidedServiceCancellationHandler;
use Database\Factories\ProvidedServiceFactory;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class ProvidedServiceEventSubscriberTest extends TestCase
{
    private ProvidedServiceEventSubscriber $subscriber;
    private ProvidedService $providedService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->subscriber = App::make(ProvidedServiceEventSubscriber::class);
        $this->providedService = ProvidedServiceFactory::new()->make(['id' => fake()->randomNumber()]);
    }

    public function testHandleRegisteredSuccess(): void
    {
        $this->mock(DecreaseDueToProvidedServiceHandler::class)
            ->shouldReceive('handle')->once();

        $event = new ProvidedServiceRegistered($this->providedService->id);
        $this->subscriber->handleRegistered($event);
    }

    public function testHandleCanceledSuccess(): void
    {
        $this->mock(IncreaseDueToProvidedServiceCancellationHandler::class)
            ->shouldReceive('handle')->once();

        $event = new RegistrationOfProvidedServiceCanceled($this->providedService->id);
        $this->subscriber->handleCanceled($event);
    }
}
