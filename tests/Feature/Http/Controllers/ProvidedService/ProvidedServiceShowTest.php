<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\ProvidedService;

use App\Models\Contract;
use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use App\Models\Service;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class ProvidedServiceShowTest extends TestCase
{
    private const string ROUTE_NAME = 'providedServices.show';

    private InsuredPerson $insuredPerson;
    private ProvidedService $providedService;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contract = Contract::factory()->for($this->company)->createOne();
        $this->insuredPerson = InsuredPerson::factory()->for($contract)->createOne();

        $this->providedService = ProvidedService::factory()
            ->for($this->company)
            ->for(Service::factory()->for($this->company))
            ->for($this->insuredPerson)
            ->createOne();
    }

    public function testSuccess(): void
    {
        $this->getJson(route(self::ROUTE_NAME, [$this->insuredPerson, $this->providedService]))
            ->assertOk()
            ->assertJson($this->providedService->withoutRelations()->toArray());
    }

    public function testNotFoundFail(): void
    {
        $nonExistentProvidedServiceId = fake()->numberBetween(100);

        $this->getJson(route(self::ROUTE_NAME, [$this->insuredPerson, $nonExistentProvidedServiceId]))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME, [$this->insuredPerson, $this->providedService]))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME, [$this->insuredPerson, $this->providedService]))
            ->assertUnauthorized();
    }
}
