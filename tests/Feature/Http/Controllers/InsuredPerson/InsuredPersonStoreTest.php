<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\InsuredPerson;

use App\Events\InsuredPerson\InsuredPersonAdded;
use App\Models\Contract;
use App\Models\InsuredPerson;
use App\Models\Person;
use Database\Factories\InsuredPersonFactory;
use Illuminate\Support\Facades\Event;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InsuredPersonStoreTest extends TestCase
{
    private const string ROUTE_NAME = 'insuredPerson.store';

    private Contract $contract;
    private InsuredPersonFactory $insuredPersonFactory;
    private array $formData;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $personFactory = Person::factory()->for($this->company);
        $this->contract = Contract::factory()->for($this->company)->createOne();
        $this->insuredPersonFactory = InsuredPerson::factory()->for($this->contract)->for($personFactory);
        $this->formData = $this->insuredPersonFactory->makeOne()->toArray();
    }

    public function testSuccess(): void
    {
        Event::fake();

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(InsuredPerson::class, $this->formData);
        Event::assertDispatched(InsuredPersonAdded::class);
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
            ['person_id', null, 'The person id field is required'],
            ['policy_number', null, 'The policy number field is required'],
            ['is_allowed_to_exceed_limit', null, 'The is allowed to exceed limit field is required'],
            ['is_allowed_to_exceed_limit', null, 'The is allowed to exceed limit field must be true or false.'],
            ['person_id', -1, 'The selected person id is invalid'],
        ];
    }

    #[DataProvider('uniqueFieldsProvider')]
    public function testUniqueFields(string $field, string $expectedMessage): void
    {
        $existingValue = $this->insuredPersonFactory->createOne()->getAttribute($field);
        $this->formData[$field] = $existingValue;

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $expectedMessage]);
    }

    public static function uniqueFieldsProvider(): array
    {
        return [
            ['person_id', 'The person id has already been taken'],
            ['policy_number', 'The policy number has already been taken']
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
