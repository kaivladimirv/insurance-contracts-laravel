<?php

declare(strict_types=1);

namespace Tests\Database\Commands;

use App\Console\Commands\RecalcBalancesForService;
use App\Models\Balance;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use App\Models\Service;
use Database\Factories\ContractServiceFactory;
use Database\Factories\InsuredPersonFactory;
use Database\Factories\ProvidedServiceFactory;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class RecalcBalanceForServiceCommandTest extends TestCase
{
    private ContractService $contractService;

    /**
     * @var Collection<int, ProvidedService>
     */
    private Collection $providedServices;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contract = Contract::factory()->for($this->company)->createOne();
        $this->contractService = ContractServiceFactory::new()
            ->for($contract)
            ->withLimitQuantity(10)
            ->createOne(['service_id' => Service::factory()->for($this->company)]);

        $insuredPersons = InsuredPersonFactory::new()->for($contract)->createMany(2);

        $this->providedServices = new Collection();
        $insuredPersons->map(function (InsuredPerson $insuredPerson) {
            $this->providedServices->push(
                ProvidedServiceFactory::new()
                    ->for($insuredPerson)
                    ->for($this->contractService)
                    ->createOne()
            );
        });
    }

    public function testSuccess(): void
    {
        $this->artisan(RecalcBalancesForService::class, [
            'contractId' => $this->contractService->contract_id,
            'serviceId' => $this->contractService->service_id
        ])->assertSuccessful();

        $this->assertDatabaseHas(
            Balance::class,
            [
                'insured_person_id' => $this->providedServices[0]->insured_person_id,
                'service_id' => $this->providedServices[0]->service_id,
                'balance' => $this->contractService->limit_value - $this->providedServices[0]->getValue()
            ]
        );
        $this->assertDatabaseHas(
            Balance::class,
            [
                'insured_person_id' => $this->providedServices[1]->insured_person_id,
                'service_id' => $this->providedServices[1]->service_id,
                'balance' => $this->contractService->limit_value - $this->providedServices[1]->getValue()
            ]
        );
    }
}
