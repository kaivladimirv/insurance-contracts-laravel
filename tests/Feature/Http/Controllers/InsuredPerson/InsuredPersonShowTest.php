<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\InsuredPerson;

use App\Models\Contract;
use App\Models\InsuredPerson;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class InsuredPersonShowTest extends TestCase
{
    private const string ROUTE_NAME = 'insuredPerson.show';

    private Contract $contract;
    private InsuredPerson $insuredPerson;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contract = Contract::factory()->for($this->company)->createOne();
        $this->insuredPerson = InsuredPerson::factory()->for($this->contract)->createOne();
    }

    public function testSuccess(): void
    {
        $this->getJson(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]))
            ->assertOk()
            ->assertJson($this->insuredPerson->toArray());
    }

    public function testNotFoundFail(): void
    {
        $nonExistentInsuredPersonId = fake()->numberBetween(100);

        $this->getJson(route(self::ROUTE_NAME, [$this->contract, $nonExistentInsuredPersonId]))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]))
            ->assertUnauthorized();
    }
}
