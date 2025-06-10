<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\ContractService;

use App\Events\ContractService\ServiceAddedToContract;
use App\Models\Contract;
use App\Models\ContractService;
use Database\Factories\ContractServiceFactory;
use Illuminate\Support\Facades\Event;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public function testServiceIdUniqueFail(): void
    {
        $existingContractService = $this->contractServiceFactory->createOne();

        $this->formData['service_id'] = $existingContractService->service_id;

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['service_id' => 'The service id has already been taken']);
    }


    #[DataProvider('invalidDataProvider')]
    public function testInvalidData(string $field, mixed $invalidValue, string $expectedMessage): void
    {
        $this->formData[$field] = $invalidValue;

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $expectedMessage]);
    }

    public static function invalidDataProvider(): array
    {
        return [
            ['service_id', null, 'The service id field is required'],
            ['service_id', -1, 'The selected service id is invalid'],
            ['limit_type', 123, 'The selected limit type is invalid'],
            ['limit_type', null, 'The limit type field is required'],
            ['limit_value', null, 'The limit value field is required']
        ];
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
