<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Contract;

use App\Models\Contract;
use Override;
use Tests\TestCase;

class ContractShowTest extends TestCase
{
    private const string ROUTE_NAME = 'contracts.show';

    private Contract $contract;
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contract = Contract::factory()->for($this->company)->createOne();
    }

    public function testSuccess(): void
    {
        $this->getJson(route(self::ROUTE_NAME, $this->contract))
            ->assertOk()
            ->assertJson($this->contract->toArray());
    }

    public function testNotFoundFail(): void
    {
        $nonExistentContractId = fake()->numberBetween(100);

        $this->getJson(route(self::ROUTE_NAME, $nonExistentContractId))
            ->assertNotFound();
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
