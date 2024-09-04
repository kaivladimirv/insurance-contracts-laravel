<?php

declare(strict_types=1);

namespace Tests\Feature\UseCases\Balance\Decrease\DueToProvidedService;

use App\Enums\NotifierType;
use App\Models\Balance;
use App\Models\Contract;
use App\Models\ProvidedService;
use App\Models\Service;
use App\Notifications\Balance\BalanceDecreasedDueToProvidedService;
use App\UseCases\Balance\Decrease\DueToProvidedService\DecreaseDueToProvidedServiceCommand;
use App\UseCases\Balance\Decrease\DueToProvidedService\DecreaseDueToProvidedServiceHandler;
use Database\Factories\BalanceFactory;
use Database\Factories\ContractServiceFactory;
use Database\Factories\InsuredPersonFactory;
use Database\Factories\PersonFactory;
use Database\Factories\ProvidedServiceFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DecreaseDueToProvidedServiceTest extends TestCase
{
    private ProvidedService $providedService;
    private DecreaseDueToProvidedServiceHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contract = Contract::factory()->for($this->company)->createOne();
        $this->contractService = ContractServiceFactory::new()->for($contract)
            ->withLimitQuantity(10)
            ->createOne(['service_id' => Service::factory()->for($this->company)]);

        $person = PersonFactory::new()->for($this->company)->createOne(['notifier_type' => NotifierType::EMAIL]);
        $this->insuredPerson = InsuredPersonFactory::new()->for($contract)->for($person)->createOne();

        $this->providedService = ProvidedServiceFactory::new()
            ->for($this->insuredPerson)
            ->for($this->contractService)
            ->createOne(['quantity' => 5]);

        $this->handler = App::make(DecreaseDueToProvidedServiceHandler::class);
    }

    public function testSuccess(): void
    {
        Notification::fake();
        BalanceFactory::new()->for($this->insuredPerson)->for($this->contractService)->createOne();

        $command = new DecreaseDueToProvidedServiceCommand($this->providedService->id);
        $this->handler->handle($command);

        $expectedBalance = $this->contractService->limit_value - $this->providedService->getValue();
        $this->assertDatabaseHas(
            Balance::class,
            [
                'insured_person_id' => $this->providedService->insured_person_id,
                'service_id' => $this->providedService->service_id,
                'balance' => $expectedBalance
            ]
        );

        Notification::assertSentTo($this->insuredPerson->person, BalanceDecreasedDueToProvidedService::class);
    }

    public function testIfBalanceIsNotFilledSuccess(): void
    {
        Notification::fake();

        $command = new DecreaseDueToProvidedServiceCommand($this->providedService->id);
        $this->handler->handle($command);

        $expectedBalance = $this->contractService->limit_value - $this->providedService->getValue();
        $this->assertDatabaseHas(
            Balance::class,
            [
                'insured_person_id' => $this->providedService->insured_person_id,
                'service_id' => $this->providedService->service_id,
                'balance' => $expectedBalance
            ]
        );

        Notification::assertSentTo($this->insuredPerson->person, BalanceDecreasedDueToProvidedService::class);
    }
}
