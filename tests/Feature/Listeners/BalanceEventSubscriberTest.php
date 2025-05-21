<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Enums\NotifierType;
use App\Events\Balance\BalanceWasDecreasedDueToProvidedService;
use App\Events\Balance\BalanceWasIncreasedDueToProvidedService;
use App\Listeners\BalanceEventSubscriber;
use App\Notifications\Balance\BalanceDecreasedDueToProvidedService;
use App\Notifications\Balance\BalanceIncreasedDueToProvidedServiceCancellation;
use Database\Factories\BalanceFactory;
use Database\Factories\InsuredPersonFactory;
use Database\Factories\PersonFactory;
use Illuminate\Support\Facades\Notification;
use Override;
use Database\Factories\ProvidedServiceFactory;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class BalanceEventSubscriberTest extends TestCase
{
    private BalanceEventSubscriber $subscriber;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->subscriber = App::make(BalanceEventSubscriber::class);

        $this->person = PersonFactory::new()->for($this->company)->createOne(['notifier_type' => NotifierType::EMAIL]);
        $insuredPerson = InsuredPersonFactory::new()->for($this->person)->createOne();
        $this->providedService = ProvidedServiceFactory::new()->for($insuredPerson)->makeOne();
        $this->balance = BalanceFactory::new()->for($insuredPerson)->makeOne();
    }

    public function testHandleBalanceWasDecreasedDueToProvidedServiceSuccess(): void
    {
        Notification::fake();

        $event = new BalanceWasDecreasedDueToProvidedService($this->balance, $this->providedService);
        $this->subscriber->handleBalanceWasDecreasedDueToProvidedService($event);

        Notification::assertSentTo($this->person, BalanceDecreasedDueToProvidedService::class);
    }

    public function testHandleBalanceWasIncreasedDueToProvidedServiceSuccess(): void
    {
        Notification::fake();

        $event = new BalanceWasIncreasedDueToProvidedService($this->balance, $this->providedService);
        $this->subscriber->handleBalanceWasIncreasedDueToProvidedService($event);

        Notification::assertSentTo($this->person, BalanceIncreasedDueToProvidedServiceCancellation::class);
    }
}
