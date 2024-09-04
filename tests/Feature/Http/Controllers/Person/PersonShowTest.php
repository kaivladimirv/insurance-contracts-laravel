<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Person;

use App\Models\Person;
use Override;
use Tests\TestCase;

class PersonShowTest extends TestCase
{
    private const string ROUTE_NAME = 'persons.show';

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
        $this->getJson(route(self::ROUTE_NAME, $this->person))
            ->assertOk()
            ->assertJson($this->person->toArray());
    }

    public function testNotFoundFail(): void
    {
        $nonExistentPersonId = fake()->numberBetween(100);

        $this->getJson(route(self::ROUTE_NAME, $nonExistentPersonId))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME, $this->person))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME, $this->person))
            ->assertUnauthorized();
    }
}
