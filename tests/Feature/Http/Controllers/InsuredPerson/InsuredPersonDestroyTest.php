<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\InsuredPerson;

use App\Models\Contract;
use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use App\Models\Service;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class InsuredPersonDestroyTest extends TestCase
{
    private const string ROUTE_NAME = 'insuredPerson.destroy';

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
        $this->delete(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]))
            ->assertNoContent();

        $this->assertModelMissing($this->insuredPerson);
    }

    public function testServicesHaveAlreadyBeenProvidedFail(): void
    {
        ProvidedService::factory()
            ->for($this->company)
            ->for($this->contract)
            ->for($this->insuredPerson)
            ->for(Service::factory()->for($this->company))
            ->createOne();

        $this->delete(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]))
            ->assertConflict();
    }

    public function testNotFoundFail(): void
    {
        $nonExistentInsuredPersonId = fake()->numberBetween(100);

        $this->deleteJson(route(self::ROUTE_NAME, [$this->contract, $nonExistentInsuredPersonId]))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->deleteJson(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->deleteJson(route(self::ROUTE_NAME, [$this->contract, $this->insuredPerson]))
            ->assertUnauthorized();
    }
}
