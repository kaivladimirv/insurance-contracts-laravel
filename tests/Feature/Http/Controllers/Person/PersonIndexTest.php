<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Person;

use App\Enums\NotifierType;
use App\Models\Person;
use Database\Factories\PersonFactory;
use Override;
use Tests\TestCase;

class PersonIndexTest extends TestCase
{
    private const string ROUTE_NAME = 'persons.index';

    private PersonFactory $personFactory;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->personFactory = Person::factory()->for($this->company);
        $this->personFactory->count(1)->create(['notifier_type' => fake()->randomElement(NotifierType::cases())]);
    }

    public function testNameFoundSuccess(): void
    {
        $this->personFactory->createOne(['last_name' => 'Tester1', 'first_name' => 'Test1', 'middle_name' => 'Testerovich1']);
        $this->personFactory->createOne(['last_name' => 'Tester2', 'first_name' => 'Test2', 'middle_name' => 'Testerovich2']);

        $params = [
            'page' => 1,
            'last_name' => 'Tester',
            'first_name' => 'Test',
            'middle_name' => 'Testerovich'
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testPhoneNumberFoundSuccess(): void
    {
        $person = $this->personFactory->createOne();

        $params = [
            'page' => 1,
            'phone_number' => $person->phone_number
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function testEmailAndNotifierTypeFoundSuccess(): void
    {
        $person = $this->personFactory->createOne(['notifier_type' => NotifierType::EMAIL]);

        $params = [
            'page' => 1,
            'email' => $person->email,
            'notifier_type' => $person->notifier_type
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function testNotificationsAreDisabledSuccess(): void
    {
        $expectedRecordCount = 3;

        $this->personFactory
            ->state(['notifier_type' => null])
            ->createMany($expectedRecordCount);

        $params = [
            'page' => 1,
            'notifier_type' => ''
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount($expectedRecordCount, 'data');
    }

    public function testLastNameNotFoundSuccess(): void
    {
        $params = [
            'page' => 1,
            'last_name' => fake()->unique()->lastName()
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }
}
