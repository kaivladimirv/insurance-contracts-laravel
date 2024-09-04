<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Service;

use App\Models\Service;
use Override;
use Tests\TestCase;

class ServiceStoreTest extends TestCase
{
    private const string ROUTE_NAME = 'services.store';

    private array $formData;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->formData = Service::factory()->make()->toArray();
    }

    public function testSuccess(): void
    {
        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(Service::class, $this->formData);
    }

    public function testNameRequiredFail(): void
    {
        $this->formData['name'] = '';

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name' => 'The name field is required']);
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnauthorized();
    }
}
