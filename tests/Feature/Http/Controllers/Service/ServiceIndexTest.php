<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Service;

use App\Models\Service;
use Database\Factories\ServiceFactory;
use Override;
use Tests\TestCase;

class ServiceIndexTest extends TestCase
{
    private const string ROUTE_NAME = 'services.index';

    private ServiceFactory $serviceFactory;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->serviceFactory = Service::factory()->for($this->company);
    }

    public function testSuccess(): void
    {
        $this->serviceFactory->count(5)->create();
        $this->serviceFactory->createOne(['name' => 'test1']);
        $this->serviceFactory->createOne(['name' => 'test2']);

        $params = [
            'page' => 1,
            'name' => 'test'
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testNameNotFoundSuccess(): void
    {
        $this->serviceFactory->count(33)->create();

        $params = [
            'page' => 1,
            'name' => 'test'
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
