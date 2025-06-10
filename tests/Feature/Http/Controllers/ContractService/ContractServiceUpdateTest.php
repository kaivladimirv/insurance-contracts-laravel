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
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[DataProvider('invalidDataProvider')]
    public function testInvalidData(string $field, mixed $invalidValue, string $expectedMessage): void
    {
        $this->formData[$field] = $invalidValue;

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $this->contractService->service_id]), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $expectedMessage]);
    }

    public static function invalidDataProvider(): array
    {
        return [
            ['limit_type', 123, 'The selected limit type is invalid'],
            ['limit_type', null, 'The limit type field is required'],
            ['limit_value', null, 'The limit value field is required']
        ];
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
