<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Person;

use App\Models\Contract;
use App\Models\InsuredPerson;
use App\Models\Person;
use Override;
use Tests\TestCase;

class PersonDestroyTest extends TestCase
{
    private const string ROUTE_NAME = 'persons.destroy';

    private Person $person;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->person = Person::factory()->for($this->company)->createOne();
    }

    public function testSuccess(): void
    {
        $this->delete(route(self::ROUTE_NAME, $this->person))
            ->assertNoContent();

        $this->assertModelMissing($this->person);
    }

    public function testUsedInContractsFail(): void
    {
        $contract = Contract::factory()->for($this->company)->createOne();
        InsuredPerson::factory()->for($contract)->for($this->person)->createOne();

        $this->deleteJson(route(self::ROUTE_NAME, $this->person))
            ->assertConflict();
    }

    public function testNotFoundFail(): void
    {
        $nonExistentPersonId = fake()->numberBetween(100);

        $this->deleteJson(route(self::ROUTE_NAME, $nonExistentPersonId))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->deleteJson(route(self::ROUTE_NAME, $this->person))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->deleteJson(route(self::ROUTE_NAME, $this->person))
            ->assertUnauthorized();
    }
}
