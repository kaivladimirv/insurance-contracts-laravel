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

class DebtorsIndexTest extends TestCase
{
    private const string ROUTE_NAME = 'debtors.index';

    private ServiceFactory|Factory $serviceFactory;
    private BalanceFactory|Factory $balanceFactory;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->serviceFactory = Service::factory()->for($this->company);
        $contractFactory = Contract::factory()->for($this->company);
        $insuredPersonFactory = InsuredPerson::factory()->for($contractFactory);

        $this->balanceFactory = Balance::factory()
            ->state(
                [
                    'service_id' => $this->serviceFactory,
                    'contract_id' => $contractFactory,
                    'insured_person_id' => $insuredPersonFactory
                ]
            );

        $this->balanceFactory->withQuantityBalanceBetween()->count(10)->create();
        $this->balanceFactory->withAmountBalanceBetween()->count(10)->create();
    }

    public function testSuccess(): void
    {
        $this->balanceFactory->withQuantityBalanceBetween(-30, -1)->count(3)->create();
        $this->balanceFactory->withAmountBalanceBetween(-30000, -1000)->count(3)->create();

        $params = [
            'page' => 1
        ];
        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount(6, 'data');
    }

    public function testRangeSuccess(): void
    {
        $this->balanceFactory->withQuantityBalanceBetween(-30, -1)->count(3)->create();
        $this->balanceFactory->withAmountBalanceBetween(-30000, -1000)->count(3)->create();

        $params = [
            'page' => 1,
            'debt_from' => 1,
            'debt_to' => 40
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testServiceIdSuccess(): void
    {
        $this->balanceFactory->withQuantityBalanceBetween(-30, -1)->count(3)->create();

        $service = $this->serviceFactory->createOne();
        $this->balanceFactory->withAmountBalanceBetween(-30000, -1000)->count(2)->create(['service_id' => $service]);

        $params = [
            'page' => 1,
            'service_id' => $service->id
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testServiceIdNotFoundFail(): void
    {
        $params = [
            'page' => 1,
            'service_id' => fake()->numberBetween('100')
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertUnprocessable()
            ->assertInvalid('service_id');
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }
}
