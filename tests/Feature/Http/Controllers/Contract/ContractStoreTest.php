<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Contract;

use App\Models\Contract;
use Database\Factories\ContractFactory;
use Override;
use Tests\TestCase;

class ContractStoreTest extends TestCase
{
    private const string ROUTE_NAME = 'contracts.store';

    private ContractFactory $contractFactory;
    private array $formData;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contractFactory = Contract::factory()->for($this->company);
        $this->formData = $this->contractFactory->make()->toArray();
    }

    public function testSuccess(): void
    {
        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(Contract::class, $this->formData);
    }

    public function testNumberRequiredFail(): void
    {
        unset($this->formData['number']);

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['number' => 'The number field is required']);
    }

    public function testStartDateRequiredFail(): void
    {
        unset($this->formData['start_date']);

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['start_date' => 'The start date field is required']);
    }

    public function testEndDateRequiredFail(): void
    {
        unset($this->formData['end_date']);

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date' => 'The end date field is required']);
    }

    public function testEndDateAfterOrEqualStartDateFail(): void
    {
        $this->formData['end_date'] = date_create($this->formData['start_date'])->modify('-1 days')->format('Y-m-d');

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date' => 'The end date field must be a date after or equal to start date']);
    }

    public function testMaxAmountRequiredFail(): void
    {
        unset($this->formData['max_amount']);

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['max_amount' => 'The max amount field is required']);
    }

    public function testNumberUniqueFail(): void
    {
        $this->formData['number'] = $this->contractFactory->createOne()->number;

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['number' => 'The number has already been taken']);
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnauthorized();
    }
}
