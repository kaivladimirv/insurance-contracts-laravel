<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\ContractService;

use App\Events\ContractService\ServiceAddedToContract;
use App\Models\Contract;
use App\Models\ContractService;
use Database\Factories\ContractServiceFactory;
use Illuminate\Support\Facades\Event;
use Override;
use Tests\TestCase;

class ContractServiceStoreTest extends TestCase
{
    private const string ROUTE_NAME = 'contractServices.store';

    private Contract $contract;
    private ContractServiceFactory $contractServiceFactory;
    private array $formData;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contract = Contract::factory()->for($this->company)->createOne();
        $this->contractServiceFactory = ContractService::factory()->for($this->contract);
        $this->formData = $this->contractServiceFactory->makeOne()->toArray();
    }

    public function testSuccess(): void
    {
        Event::fake();

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertNoContent();

        $this->assertDatabaseHas(ContractService::class, $this->formData);
        Event::assertDispatched(ServiceAddedToContract::class);
    }

    public function testServiceIdRequiredFail(): void
    {
        $this->formData['service_id'] = null;

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['service_id' => 'The service id field is required']);
    }

    public function testServiceIdDoesNotExistFail(): void
    {
        $this->formData['service_id'] = -1;

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['service_id' => 'The selected service id is invalid']);
    }

    public function testServiceIdUniqueFail(): void
    {
        $existingContractService = $this->contractServiceFactory->createOne();

        $this->formData['service_id'] = $existingContractService->service_id;

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['service_id' => 'The service id has already been taken']);
    }

    public function testLimitTypeDoesNotExistFail(): void
    {
        $this->formData['limit_type'] = 123;

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit_type' => 'The selected limit type is invalid']);
    }

    public function testLimitTypeRequiredFail(): void
    {
        unset($this->formData['limit_type']);

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit_type' => 'The limit type field is required']);
    }

    public function testLimitValueRequiredFail(): void
    {
        unset($this->formData['limit_value']);

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit_value' => 'The limit value field is required']);
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnauthorized();
    }
}
