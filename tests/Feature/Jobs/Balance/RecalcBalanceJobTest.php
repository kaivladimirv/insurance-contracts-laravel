<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Balance;

use App\Jobs\Balance\RecalcBalance;
use App\Models\Balance;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use App\Models\Service;
use Database\Factories\BalanceFactory;
use Database\Factories\ContractServiceFactory;
use Database\Factories\InsuredPersonFactory;
use Database\Factories\ProvidedServiceFactory;
use Tests\TestCase;

class RecalcBalanceJobTest extends TestCase
{
    private InsuredPerson $insuredPerson;
    private ContractService $contractService;
    private ProvidedService $providedService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contract = Contract::factory()->for($this->company)->createOne();
        $this->contractService = ContractServiceFactory::new()
            ->withLimitQuantity(10)
            ->createOne(
                [
                    'contract_id' => $contract->id,
                    'service_id' => Service::factory()->for($this->company)
                ]
            );

        $this->insuredPerson = InsuredPersonFactory::new()->for($contract)->createOne();

        $this->providedService = ProvidedServiceFactory::new()
            ->for($this->insuredPerson)
            ->for($this->contractService)
            ->createOne(['quantity' => 5]);
    }

    public function testSuccess(): void
    {
        BalanceFactory::new()->for($this->insuredPerson)->for($this->contractService)->createOne();

        RecalcBalance::dispatch(
            $this->providedService->contract_id,
            $this->providedService->insured_person_id,
            $this->providedService->service_id
        );

        $expectedBalance = $this->contractService->limit_value - $this->providedService->getValue();

        $this->assertDatabaseHas(
            Balance::class,
            [
                'insured_person_id' => $this->providedService->insured_person_id,
                'service_id' => $this->providedService->service_id,
                'balance' => $expectedBalance
            ]
        );
    }

    public function testBalanceTableIsNotFilledInSuccess(): void
    {
        RecalcBalance::dispatch(
            $this->providedService->contract_id,
            $this->providedService->insured_person_id,
            $this->providedService->service_id
        );

        $expectedBalance = $this->contractService->limit_value - $this->providedService->getValue();

        $this->assertDatabaseHas(
            Balance::class,
            [
                'insured_person_id' => $this->providedService->insured_person_id,
                'service_id' => $this->providedService->service_id,
                'balance' => $expectedBalance
            ]
        );
    }
}
