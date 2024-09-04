<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Contract;

use App\Models\Contract;
use App\Models\ContractService;
use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use App\Models\Service;
use Override;
use Tests\TestCase;

class ContractDestroyTest extends TestCase
{
    private const string ROUTE_NAME = 'contracts.destroy';

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
        $this->delete(route(self::ROUTE_NAME, $this->contract->id))
            ->assertNoContent();

        $this->assertSoftDeleted($this->contract);
    }

    public function testServicesWereProvidedUnderContractFail(): void
    {
        $insuredPerson = InsuredPerson::factory()->for($this->contract)->createOne();
        $service = Service::factory()->for($this->company)->createOne();
        $contractService = ContractService::factory()->for($this->contract)->for($service)
            ->withLimitSum(200)->createOne();

        ProvidedService::factory()
            ->for($insuredPerson)
            ->for($contractService)
            ->createOne(
                [
                    'quantity' => 1,
                    'price' => 100
                ]
            );

        $this->deleteJson(route(self::ROUTE_NAME, $this->contract))
            ->assertConflict();
    }

    public function testNotFoundFail(): void
    {
        $nonExistentContractId = fake()->numberBetween(100);

        $this->deleteJson(route(self::ROUTE_NAME, $nonExistentContractId))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->deleteJson(route(self::ROUTE_NAME, $this->contract))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->deleteJson(route(self::ROUTE_NAME, $this->contract))
            ->assertUnauthorized();
    }
}
