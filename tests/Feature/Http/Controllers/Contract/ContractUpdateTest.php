<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Contract;

use App\Models\Contract;
use Database\Factories\ContractFactory;
use Override;
use Tests\TestCase;

class ContractUpdateTest extends TestCase
{
    private const string ROUTE_NAME = 'contracts.update';

    private ContractFactory $contractFactory;
    private Contract $contract;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contractFactory = Contract::factory()->for($this->company);
        $this->contract = $this->contractFactory->createOne();
    }

    public function testSuccess(): void
    {
        $formData = $this->contractFactory->make()->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $formData)
            ->assertNoContent();

        $this->assertDatabaseHas(Contract::class, array_merge($formData, ['id' => $this->contract->id]));
    }

    public function testNumberRequiredFail(): void
    {
        $formData = $this->contract->makeHidden('number')->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['number' => 'The number field is required']);
    }

    public function testStartDateRequiredFail(): void
    {
        $formData = $this->contract->makeHidden('start_date')->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['start_date' => 'The start date field is required']);
    }

    public function testEndDateRequiredFail(): void
    {
        $formData = Contract::factory()->make(['end_date' => null])->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date' => 'The end date field is required']);
    }

    public function testEndDateAfterOrEqualStartDateFail(): void
    {
        $formData = $this->contract->fill(['start_date' => now(), 'end_date' => now()->subDay()])->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date' => 'The end date field must be a date after or equal to start date']);
    }

    public function testMaxAmountRequiredFail(): void
    {
        $formData = $this->contract->makeHidden('max_amount')->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['max_amount' => 'The max amount field is required']);
    }

    public function testNumberUniqueFail(): void
    {
        $existingNumber = $this->contractFactory->createOne()->number;
        $formData = $this->contract->setAttribute('number', $existingNumber)->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['number' => 'The number has already been taken']);
    }

    public function testNotFoundFail(): void
    {
        $nonExistentContractId = fake()->numberBetween(100);
        $formData = Contract::factory()->make()->toArray();

        $this->postJson(route(self::ROUTE_NAME, $nonExistentContractId), $formData)
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME, $this->contract))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME, $this->contract))
            ->assertUnauthorized();
    }
}
