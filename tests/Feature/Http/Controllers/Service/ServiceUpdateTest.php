<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Service;

use App\Models\Service;
use Database\Factories\ServiceFactory;
use Override;
use Tests\TestCase;

class ServiceUpdateTest extends TestCase
{
    private const string ROUTE_NAME = 'services.update';

    private ServiceFactory $serviceFactory;
    private Service $service;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->serviceFactory = Service::factory()->for($this->company);
        $this->service = $this->serviceFactory->createOne();
    }

    public function testSuccess(): void
    {
        $formData = $this->serviceFactory->make()->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->service), $formData)
            ->assertNoContent();
    }

    public function testNameRequiredFail(): void
    {
        $formData = $this->service->makeHidden('name')->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->service), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name' => 'The name field is required']);
    }

    public function testNotFoundFail(): void
    {
        $nonExistentServiceId = fake()->numberBetween(100);
        $formData = Service::factory()->make()->toArray();

        $this->postJson(route(self::ROUTE_NAME, $nonExistentServiceId), $formData)
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME, $this->service))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME, $this->service))
            ->assertUnauthorized();
    }
}
