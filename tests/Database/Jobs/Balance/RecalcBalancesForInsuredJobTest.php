<?php

declare(strict_types=1);

namespace Tests\Database\Jobs\Balance;

use App\Jobs\Balance\RecalcBalancesForInsured;
use App\Models\Balance;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\ProvidedService;
use Database\Factories\BalanceFactory;
use Database\Factories\ContractServiceFactory;
use Database\Factories\InsuredPersonFactory;
use Database\Factories\ProvidedServiceFactory;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class RecalcBalancesForInsuredJobTest extends TestCase
{
    /** @var Collection<int, ContractService> */
    private Collection $contractServices;
    /** @var Collection<int, ProvidedService> */
    private \Illuminate\Support\Collection $providedServices;
    private array $insuredIds;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contract = Contract::factory()->for($this->company)->createOne();
        $this->contractServices = ContractServiceFactory::new()->for($contract)->withLimitQuantity()->createMany(2);

        $this->insuredIds = InsuredPersonFactory::new()->for($contract)->createMany(2)
            ->pluck('id')->map(fn(int $id) => ['insured_person_id' => $id])->all();

        $providedServiceFactory = ProvidedServiceFactory::new(['quantity' => 1])->forEachSequence(...$this->insuredIds);
        $this->providedServices = $this->contractServices->flatMap(function ($contractService) use ($providedServiceFactory) {
            return $providedServiceFactory->for($contractService)->create();
        })->collect();
    }

    public function testSuccess(): void
    {
        $this->contractServices->each(function ($contractService) {
            BalanceFactory::new()->for($contractService)->forEachSequence(...$this->insuredIds)->create();
        });

        $this->contractServices->each(function (ContractService $contractService) {
            $contractService->update(['limit_value' => $contractService->limit_value + 1]);
        });

        $balances = Balance::all(['insured_person_id', 'service_id', 'balance'])->groupBy('insured_person_id')->values();
        $expectedBalancesByInsured1 = $balances->get(0)->keyBy('service_id')->toArray();
        $expectedBalancesByInsured2 = $balances->get(1)->toArray();

        $insuredId = current($expectedBalancesByInsured1)['insured_person_id'];

        foreach ($this->providedServices->where('insured_person_id', $insuredId) as $providedService) {
            $limitValue = $this->contractServices->firstWhere('service_id', $providedService->service_id)->limit_value;

            $expectedBalancesByInsured1[$providedService->service_id]['balance'] = $limitValue - $providedService->getValue();
        }

        RecalcBalancesForInsured::dispatch($insuredId);

        foreach (array_merge($expectedBalancesByInsured1, $expectedBalancesByInsured2) as $expectedBalance) {
            $this->assertDatabaseHas(Balance::class, $expectedBalance);
        }
    }
}
