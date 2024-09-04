<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\ContractService;

use App\Events\ContractService\RemoveServiceFromContract;
use App\Models\Contract;
use App\Models\ContractService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Override;
use Random\RandomException;
use Tests\TestCase;

class ContractServiceDestroyTest extends TestCase
{
    private const string ROUTE_NAME = 'contractServices.destroy';

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
        Event::fake();

        $this->delete(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]))
            ->assertNoContent();

        $this->assertModelMissing($this->contractService);
        Event::assertDispatched(RemoveServiceFromContract::class);
    }

    /**
     * @throws RandomException
     */
    public function testNotFoundFail(): void
    {
        $nonExistentServiceId = fake()->numberBetween(100);

        $this->deleteJson(route(self::ROUTE_NAME, [$this->contract, $nonExistentServiceId]))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->deleteJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->deleteJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]))
            ->assertUnauthorized();
    }
}
