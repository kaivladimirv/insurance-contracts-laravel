<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use App\Models\Company;
use Override;
use Tests\TestCase;

class CompanyConfirmTest extends TestCase
{
    private const string ROUTE_NAME = 'company.confirm';

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->createOne();
    }

    public function testSuccess(): void
    {
        $this->patch(route(self::ROUTE_NAME, $this->company->email_confirm_token))
            ->assertNoContent();

        $this->assertDatabaseHas(
            Company::class,
            [
                'id' => $this->company->id,
                'is_email_confirmed' => true,
                'email_confirm_token' => null
            ]
        );
    }

    public function testTokenNotFoundFail(): void
    {
        $invalidEmailConfirmToken = '1234567';

        $this->patch(route(self::ROUTE_NAME, $invalidEmailConfirmToken))
            ->assertNotFound();
    }
}
