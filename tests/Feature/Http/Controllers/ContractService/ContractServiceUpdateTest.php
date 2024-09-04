<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\ContractService;

use App\Enums\LimitType;
use App\Events\ContractService\ServiceUpdatedToContract;
use App\Exceptions\InUse;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use Illuminate\Support\Facades\Event;
use Override;
use Random\RandomException;
use Tests\TestCase;

class ContractServiceUpdateTest extends TestCase
{
    private const string ROUTE_NAME = 'contractServices.update';

    private Contract $contract;
    private ContractService $contractService;

    private array $formData;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contract = Contract::factory()->for($this->company)->createOne();
        $this->contractService = ContractService::factory()->for($this->contract)->createOne();
        $this->formData = ContractService::factory()->makeOne(
            [
                'service_id' => $this->contractService->service_id,
                'limit_value' => $this->contractService->limit_value + 1
            ]
        )->toArray();
    }

    public function testSuccess(): void
    {
        Event::fake();

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]), $this->formData)
            ->assertNoContent();

        $this->assertDatabaseHas(ContractService::class, array_merge($this->formData, ['id' => $this->contractService->id]));
        Event::assertDispatched(ServiceUpdatedToContract::class);
    }

    public function testLimitTypeDoesNotExistFail(): void
    {
        $this->formData['limit_type'] = 123;

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit_type' => 'The selected limit type is invalid']);
    }

    public function testLimitTypeRequiredFail(): void
    {
        unset($this->formData['limit_type']);

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit_type' => 'The limit type field is required']);
    }

    public function testLimitValueRequiredFail(): void
    {
        unset($this->formData['limit_value']);

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit_value' => 'The limit value field is required']);
    }

    public function testWasProvidedFail(): void
    {
        $this->formData['limit_type'] = ($this->contractService->limit_type->isItQuantityLimiter() ? LimitType::SUM : LimitType::QUANTITY);

        ProvidedService::factory()
            ->for($this->contractService)
            ->for(InsuredPerson::factory()->for($this->contract))
            ->createOne();

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]), $this->formData)
            ->assertConflict()
            ->assertContent('The limit type cannot be changed because service already provided');

        $this->expectException(InUse::class);
        $this->withoutExceptionHandling()
            ->postJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]), $this->formData);
    }

    /**
     * @throws RandomException
     */
    public function testNotFoundFail(): void
    {
        $nonExistentServiceId = fake()->numberBetween(100);

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $nonExistentServiceId]), $this->formData)
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]))
            ->assertUnauthorized();
    }
}
