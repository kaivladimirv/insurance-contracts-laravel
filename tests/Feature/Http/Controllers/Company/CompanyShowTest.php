<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use Override;
use Tests\TestCase;

class CompanyShowTest extends TestCase
{
    private const string ROUTE_NAME = 'company.show';

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();
    }

    public function testSuccess(): void
    {
        $this->getJson(route(self::ROUTE_NAME))
            ->assertOk()
            ->assertJsonStructure(['name', 'email']);
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
