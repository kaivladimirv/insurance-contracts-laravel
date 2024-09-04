<?php

declare(strict_types=1);

namespace Tests\Feature\UseCases\Balance\Increase\DueToProvidedServiceCancellation;

use App\Enums\NotifierType;
use App\Models\Balance;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use App\Models\Service;
use App\Notifications\Balance\BalanceIncreasedDueToProvidedServiceCancellation;
use App\UseCases\Balance\Increase\DueToProvidedServiceCancellation\IncreaseDueToProvidedServiceCancellationCommand;
use App\UseCases\Balance\Increase\DueToProvidedServiceCancellation\IncreaseDueToProvidedServiceCancellationHandler;
use Database\Factories\BalanceFactory;
use Database\Factories\ContractServiceFactory;
use Database\Factories\InsuredPersonFactory;
use Database\Factories\PersonFactory;
use Database\Factories\ProvidedServiceFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class IncreaseDueToProvidedServiceCancellationTest extends TestCase
{
    private ProvidedService $providedService;
    private InsuredPerson $insuredPerson;
    private ContractService $contractService;

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
            ->trashed()
            ->createOne(['quantity' => 5]);
    }

    public function testSuccess(): void
    {
        Notification::fake();
        BalanceFactory::new()->for($this->insuredPerson)->for($this->contractService)->createOne();

        $command = new IncreaseDueToProvidedServiceCancellationCommand($this->providedService->id);
        App::make(IncreaseDueToProvidedServiceCancellationHandler::class)->handle($command);

        $expectedBalance = $this->contractService->limit_value + $this->providedService->getValue();

        $this->assertDatabaseHas(
            Balance::class,
            [
                'insured_person_id' => $this->providedService->insured_person_id,
                'service_id' => $this->providedService->service_id,
                'balance' => $expectedBalance
            ]
        );

        Notification::assertSentTo($this->insuredPerson->person, BalanceIncreasedDueToProvidedServiceCancellation::class);
    }

    public function testIfBalanceIsNotFilledSuccess(): void
    {
        Notification::fake();

        $command = new IncreaseDueToProvidedServiceCancellationCommand($this->providedService->id);
        App::make(IncreaseDueToProvidedServiceCancellationHandler::class)->handle($command);


        $expectedBalance = $this->contractService->limit_value + $this->providedService->getValue();

        $this->assertDatabaseHas(
            Balance::class,
            [
                'insured_person_id' => $this->providedService->insured_person_id,
                'service_id' => $this->providedService->service_id,
                'balance' => $expectedBalance
            ]
        );

        Notification::assertSentTo($this->insuredPerson->person, BalanceIncreasedDueToProvidedServiceCancellation::class);
    }
}
