<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\InsuredPerson;

use App\Events\InsuredPerson\InsuredPersonAdded;
use App\Models\Contract;
use App\Models\InsuredPerson;
use App\Models\Person;
use Database\Factories\InsuredPersonFactory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Override;
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

        $person = Person::factory()->for($this->company)->createOne();
        $this->contract = Contract::factory()->for($this->company)->createOne();
        $this->insuredPersonFactory = InsuredPerson::factory()->for($this->contract);
        $this->formData = $this->insuredPersonFactory->for($person)->makeOne()->toArray();
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

    public function testPersonIdRequiredFail(): void
    {
        unset($this->formData['person_id']);

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['person_id' => 'The person id field is required']);
    }

    public function testPolicyNumberRequiredFail(): void
    {
        unset($this->formData['policy_number']);

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('policy_number');
    }

    public function testIsAllowedToExceedLimitRequiredFail(): void
    {
        unset($this->formData['is_allowed_to_exceed_limit']);

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('is_allowed_to_exceed_limit');
    }

    public function testPersonIdDoesNotExistFail(): void
    {
        $this->formData['person_id'] = -1;

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('person_id');
    }

    public function testPersonIdUniqueFail(): void
    {
        $existingInsuredPerson = $this->insuredPersonFactory->createOne();

        $this->formData['person_id'] = $existingInsuredPerson->person_id;

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('person_id');
    }

    public function testPolicyNumberUniqueFail(): void
    {
        $existingPolicyNumber = $this->insuredPersonFactory->createOne()->policy_number;

        $this->formData['policy_number'] = $existingPolicyNumber;

        $this->postJson(route(self::ROUTE_NAME, $this->contract), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('policy_number');
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
