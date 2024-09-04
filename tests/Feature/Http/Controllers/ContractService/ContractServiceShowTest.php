<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\ContractService;

use App\Models\Contract;
use App\Models\ContractService;
use Override;
use Tests\TestCase;

class ContractServiceShowTest extends TestCase
{
    private const string ROUTE_NAME = 'contractServices.show';

    private Contract $contract;
    private ContractService $contractService;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contract = Contract::factory()->for($this->company)->createOne();
        $this->contractService = ContractService::factory()->for($this->contract)->createOne();
    }

    public function testSuccess(): void
    {
        $this->getJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]))
            ->assertOk()
            ->assertJson($this->contractService->toArray());
    }

    public function testNotFoundFail(): void
    {
        $nonExistentServiceId = fake()->numberBetween(100);

        $this->getJson(route(self::ROUTE_NAME, [$this->contract, $nonExistentServiceId]))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]))
            ->assertUnauthorized();
    }
}
