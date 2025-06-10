<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\InsuredPerson;

use App\Models\Contract;
use App\Models\InsuredPerson;
use Database\Factories\InsuredPersonFactory;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InsuredPersonUpdateTest extends TestCase
{
    private const string ROUTE_NAME = 'insuredPerson.update';

    private Contract $contract;
    private InsuredPersonFactory $insuredPersonFactory;
    private InsuredPerson $insuredPerson;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contract = Contract::factory()->for($this->company)->createOne();
        $this->insuredPersonFactory = InsuredPerson::factory()->for($this->contract);
        $this->insuredPerson = $this->insuredPersonFactory->createOne();
    }

    public function testSuccess(): void
    {
        $formData = $this->insuredPersonFactory->make(['person_id' => $this->insuredPerson->person_id])->toArray();

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]), $formData)
            ->assertNoContent();

        $this->assertDatabaseHas(InsuredPerson::class, array_merge($formData, ['id' => $this->insuredPerson->id]));
    }

    #[DataProvider('invalidDataProvider')]
    public function testInvalidData(string $field, mixed $invalidValue, string $expectedMessage): void
    {
        $formData = $this->insuredPerson->toArray();
        $formData[$field] = $invalidValue;

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $expectedMessage]);
    }

    public static function invalidDataProvider(): array
    {
        return [
            ['policy_number', null, 'The policy number field is required'],
            ['is_allowed_to_exceed_limit', null, 'The is allowed to exceed limit field is required'],
            ['is_allowed_to_exceed_limit', null, 'The is allowed to exceed limit field must be true or false.']
        ];
    }

    public function testPolicyNumberUniqueFail(): void
    {
        $existingPolicyNumber = $this->insuredPersonFactory->createOne()->policy_number;

        $formData = $this->insuredPerson->setAttribute('policy_number', $existingPolicyNumber)->toArray();

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['policy_number' => 'The policy number has already been taken']);
    }

    public function testNotFoundFail(): void
    {
        $nonExistentInsuredPersonId = fake()->numberBetween(100);

        $formData = $this->insuredPersonFactory->make()->toArray();

        $this->postJson(route(self::ROUTE_NAME, [$this->contract, $nonExistentInsuredPersonId]), $formData)
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]))
            ->assertUnauthorized();
    }
}
