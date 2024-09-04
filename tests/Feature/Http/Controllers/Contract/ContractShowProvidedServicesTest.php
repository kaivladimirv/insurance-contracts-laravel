<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Contract;

use App\Models\Contract;
use App\Models\InsuredPerson;
use App\Models\Service;
use Database\Factories\ContractFactory;
use Database\Factories\ProvidedServiceFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Override;
use Tests\TestCase;

class ContractShowProvidedServicesTest extends TestCase
{
    private const string ROUTE_NAME = 'contracts.showProvidedServices';
    private const int PER_PAGE = 30;
    private const int TOTAL = 40;

    private Contract $contract;
    private int $pageCount;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contract = ContractFactory::new()->for($this->company)->createOne();
        ProvidedServiceFactory::new()
            ->for($this->company)
            ->for($this->contract)
            ->for(Service::factory()->for($this->company))
            ->for(InsuredPerson::factory()->for($this->contract))
            ->createMany(self::TOTAL);

        $this->pageCount = intval(ceil(self::TOTAL / self::PER_PAGE));
    }

    public function testSecondPageSuccess(): void
    {
        $pageNumber = 2;
        $previousPageCount = $pageNumber - 1;
        $expectedRecordCount = self::TOTAL - (self::PER_PAGE * $previousPageCount);

        $this->getJson(route(self::ROUTE_NAME, [$this->contract, 'page' => $pageNumber]))
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJson(fn(AssertableJson $json) => $json->has('data', $expectedRecordCount)
                ->where('meta.current_page', 2)
                ->where('meta.last_page', $this->pageCount)
                ->where('meta.per_page', self::PER_PAGE)
                ->where('meta.total', self::TOTAL)
                ->etc());
    }

    public function testNotFoundFail(): void
    {
        $nonExistentContractId = fake()->numberBetween(100);

        $this->getJson(route(self::ROUTE_NAME, $nonExistentContractId))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME, $this->contract))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME, $this->contract))
            ->assertUnauthorized();
    }
}
