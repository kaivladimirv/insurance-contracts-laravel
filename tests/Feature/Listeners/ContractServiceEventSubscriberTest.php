<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\ContractService\RemoveServiceFromContract;
use App\Events\ContractService\ServiceAddedToContract;
use App\Events\ContractService\ServiceUpdatedToContract;
use App\Jobs\Balance\RecalcBalancesForService;
use App\Jobs\Balance\RemoveBalancesForService;
use App\Listeners\ContractServiceEventSubscriber;
use App\Models\ContractService;
use Database\Factories\ContractServiceFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ContractServiceEventSubscriberTest extends TestCase
{
    private ContractServiceEventSubscriber $subscriber;
    private ContractService $contractService;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        Event::fake();

        $this->subscriber = App::make(ContractServiceEventSubscriber::class);
        $this->contractService = ContractServiceFactory::new()->makeOne();
    }

    public function testRecalcBalancesJobIsDispatchedAfterAdded(): void
    {
        $event = new ServiceAddedToContract($this->contractService->contract_id, $this->contractService->service_id);
        $this->subscriber->handleServiceAdded($event);

        Queue::assertPushed(RecalcBalancesForService::class);
    }

    public function testRecalcBalancesJobIsDispatchedAfterUpdated(): void
    {
        $event = new ServiceUpdatedToContract($this->contractService->contract_id, $this->contractService->service_id);
        $this->subscriber->handleServiceUpdated($event);

        Queue::assertPushed(RecalcBalancesForService::class);
    }

    public function testRemoveBalancesJobIsDispatchedAfterDeleted(): void
    {
        $event = new RemoveServiceFromContract($this->contractService->contract_id, $this->contractService->service_id);
        $this->subscriber->handleServiceDeleted($event);

        Queue::assertPushed(RemoveBalancesForService::class);
    }
}
