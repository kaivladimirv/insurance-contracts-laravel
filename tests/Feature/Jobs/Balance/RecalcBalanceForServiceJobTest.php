<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Balance;

use App\Jobs\Balance\RecalcBalance;
use App\Jobs\Balance\RecalcBalancesForService;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\Service;
use Database\Factories\ContractServiceFactory;
use Database\Factories\InsuredPersonFactory;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RecalcBalanceForServiceJobTest extends TestCase
{
    private ContractService $contractService;
    private int $insuredPersonCount = 3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contract = Contract::factory()->for($this->company)->createOne();
        $this->contractService = ContractServiceFactory::new()
            ->for($contract)
            ->createOne(['service_id' => Service::factory()->for($this->company)]);

        InsuredPersonFactory::new()->for($contract)->createMany($this->insuredPersonCount);
    }

    public function testSuccess(): void
    {
        Queue::fake([RecalcBalance::class]);

        RecalcBalancesForService::dispatch(
            $this->contractService->contract_id,
            $this->contractService->service_id
        );

        Queue::assertPushed(RecalcBalance::class, $this->insuredPersonCount);
    }
}
