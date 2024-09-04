<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Service;

use App\Models\Contract;
use App\Models\ContractService;
use App\Models\Service;
use Override;
use Tests\TestCase;

class ServiceDestroyTest extends TestCase
{
    private const string ROUTE_NAME = 'services.destroy';

    private Service $service;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->service = Service::factory()->for($this->company)->createOne();
    }

    public function testSuccess(): void
    {
        $this->delete(route(self::ROUTE_NAME, $this->service))
            ->assertNoContent();

        $this->assertModelMissing($this->service);
    }

    public function testUsedInContractsFail(): void
    {
        ContractService::factory()->for($this->service)->createOne();

        $this->deleteJson(route(self::ROUTE_NAME, $this->service))
            ->assertConflict();
    }

    public function testNotFoundFail(): void
    {
        $nonExistentServiceId = fake()->numberBetween(100);

        $this->deleteJson(route(self::ROUTE_NAME, $nonExistentServiceId))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->deleteJson(route(self::ROUTE_NAME, $this->service))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->deleteJson(route(self::ROUTE_NAME, $this->service))
            ->assertUnauthorized();
    }
}
