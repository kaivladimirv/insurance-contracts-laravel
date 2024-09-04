<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\ContractService;

use App\Enums\LimitType;
use App\Models\Contract;
use App\Models\ContractService;
use Database\Factories\ContractServiceFactory;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class ContractServiceIndexTest extends TestCase
{
    private const string ROUTE_NAME = 'contractServices.index';

    private Contract $contract;
    private ContractServiceFactory $contractServiceFactory;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contract = Contract::factory()->for($this->company)->createOne();
        $this->contractServiceFactory = ContractService::factory()->for($this->contract);
    }

    public function testLimitTypeSuccess(): void
    {
        $this->contractServiceFactory->count(30)->withLimitSum()->create();
        $this->contractServiceFactory->count(3)->withLimitQuantity()->create();

        $params = [
            'page' => 1,
            'limit_type' => LimitType::QUANTITY
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->contract, ...$params]))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testLimitValueSuccess(): void
    {
        $this->contractServiceFactory->count(10)->create(['limit_value' => 10]);
        $this->contractServiceFactory->count(3)->create(['limit_value' => 3]);
        $this->contractServiceFactory->count(2)->create(['limit_value' => 7]);

        $params = [
            'page' => 1,
            'limit_value_from' => 2,
            'limit_value_to' => 8
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->contract, ...$params]))
            ->assertOk()
            ->assertJsonCount(5, 'data');
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
