<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Debtors;

use App\Models\Balance;
use App\Models\Contract;
use App\Models\InsuredPerson;
use App\Models\Service;
use Database\Factories\BalanceFactory;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class DebtorsIndexByContractTest extends TestCase
{
    private const string ROUTE_NAME = 'debtors.byContract';

    private Contract $contract;
    private ServiceFactory|Factory $serviceFactory;
    private BalanceFactory|Factory $balanceFactory;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contractFactory = Contract::factory()->for($this->company);
        $this->contract = $contractFactory->createOne();

        $this->serviceFactory = Service::factory()->for($this->company);

        $insuredPersonFactory = InsuredPerson::factory()->for($this->contract);
        $this->balanceFactory = Balance::factory()->for($this->contract)->state(
            [
                'service_id' => $this->serviceFactory,
                'contract_id' => $this->contract,
                'insured_person_id' => $insuredPersonFactory
            ]
        );

        Balance::factory()->count(10)->create(
            [
                'service_id' => $this->serviceFactory,
                'contract_id' => $contractFactory,
                'insured_person_id' => $insuredPersonFactory
            ]
        );
    }

    public function testSuccess(): void
    {
        $this->balanceFactory->withQuantityBalanceBetween(-30, -1)->count(3)->create();

        $params = [
            'page' => 1
        ];
        $this->getJson(route(self::ROUTE_NAME, [$this->contract, ...$params]))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testRangeSuccess(): void
    {
        $this->balanceFactory->withQuantityBalanceBetween(-30, -1)->count(4)->create();

        $params = [
            'page' => 1,
            'debt_from' => 1,
            'debt_to' => 40
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->contract, ...$params]))
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public function testServiceIdSuccess(): void
    {
        $service = $this->serviceFactory->createOne();
        $this->balanceFactory->withAmountBalanceBetween(-30000, -1000)->count(2)->create(['service_id' => $service]);

        $params = [
            'page' => 1,
            'service_id' => $service->id
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->contract, ...$params]))
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testServiceIdNotFoundFail(): void
    {
        $params = [
            'page' => 1,
            'service_id' => -1
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->contract, ...$params]))
            ->assertUnprocessable()
            ->assertInvalid('service_id');
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME, $this->contract))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME, $this->contract))
            ->assertUnauthorized();
    }
}
