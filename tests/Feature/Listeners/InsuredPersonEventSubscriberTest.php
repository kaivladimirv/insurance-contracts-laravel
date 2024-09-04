<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\InsuredPerson\InsuredPersonAdded;
use App\Jobs\Balance\RecalcBalancesForInsured;
use App\Listeners\InsuredPersonEventSubscriber;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class InsuredPersonEventSubscriberTest extends TestCase
{
    private InsuredPersonEventSubscriber $subscriber;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->subscriber = App::make(InsuredPersonEventSubscriber::class);
    }

    public function testHandleAddedSuccess(): void
    {
        Queue::fake();

        $event = new InsuredPersonAdded(insuredPersonId: 1);
        $this->subscriber->handleAdded($event);

        Queue::assertPushed(RecalcBalancesForInsured::class);
    }
}
