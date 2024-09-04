<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Service;

use App\Models\Service;
use Override;
use Tests\TestCase;

class ServiceShowTest extends TestCase
{
    private const string ROUTE_NAME = 'services.show';

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
        $this->getJson(route(self::ROUTE_NAME, $this->service))
            ->assertOk()
            ->assertJson($this->service->toArray());
    }

    public function testNotFoundFail(): void
    {
        $nonExistentServiceId = fake()->numberBetween(100);

        $this->getJson(route(self::ROUTE_NAME, $nonExistentServiceId))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME, $this->service))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME, $this->service))
            ->assertUnauthorized();
    }
}
