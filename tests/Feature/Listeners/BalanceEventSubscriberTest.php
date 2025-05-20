<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Enums\NotifierType;
use App\Events\Balance\BalanceWasDecreasedDueToProvidedService;
use App\Listeners\BalanceEventSubscriber;
use App\Notifications\Balance\BalanceDecreasedDueToProvidedService;
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
    }

    public function testHandleBalanceWasDecreasedDueToProvidedServiceSuccess(): void
    {
        Notification::fake();

        $person = PersonFactory::new()->for($this->company)->createOne(['notifier_type' => NotifierType::EMAIL]);
        $insuredPerson = InsuredPersonFactory::new()->for($person)->createOne();
        $providedService = ProvidedServiceFactory::new()->for($insuredPerson)->makeOne();
        $balance = BalanceFactory::new()->for($insuredPerson)->makeOne();

        $event = new BalanceWasDecreasedDueToProvidedService($balance, $providedService);
        $this->subscriber->handleBalanceWasDecreasedDueToProvidedService($event);

        Notification::assertSentTo($person, BalanceDecreasedDueToProvidedService::class);
    }
}
