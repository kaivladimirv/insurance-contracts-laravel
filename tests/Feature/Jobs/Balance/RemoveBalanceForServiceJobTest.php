<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Balance;

use App\Jobs\Balance\RemoveBalancesForService;
use App\Models\Balance;
use App\Models\Contract;
use App\Models\ProvidedService;
use App\Models\Service;
use Database\Factories\BalanceFactory;
use Database\Factories\ContractServiceFactory;
use Database\Factories\InsuredPersonFactory;
use Database\Factories\ProvidedServiceFactory;
use Tests\TestCase;

class RemoveBalanceForServiceJobTest extends TestCase
{
    private ProvidedService $providedService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contract = Contract::factory()->for($this->company)->createOne();
        $contractService = ContractServiceFactory::new()->for($contract)
            ->withLimitQuantity(10)
            ->createOne(['service_id' => Service::factory()->for($this->company)]);

        $insuredPerson = InsuredPersonFactory::new()->for($contract)->createOne();

        $this->providedService = ProvidedServiceFactory::new()
            ->for($insuredPerson)
            ->for($contractService)
            ->createOne(['quantity' => 5]);

        BalanceFactory::new()->for($insuredPerson)->for($contractService)->createOne();
    }

    public function testSuccess(): void
    {
        $data = [
            'contract_id' => $this->providedService->contract_id,
            'service_id' => $this->providedService->service_id
        ];

        $this->assertDatabaseHas(Balance::class, $data);

        RemoveBalancesForService::dispatch(
            $this->providedService->contract_id,
            $this->providedService->service_id
        );

        $this->assertDatabaseMissing(Balance::class, $data);
    }
}
