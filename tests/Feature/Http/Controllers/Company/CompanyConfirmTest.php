<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use App\Models\Company;
use Illuminate\Support\Str;
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
        $this->patchJson(route(self::ROUTE_NAME, $this->company->email_confirm_token))
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

    public function testEmailConfirmTokenMax255Fail(): void
    {
        $longString = Str::random(256);

        $this->patchJson(route(self::ROUTE_NAME, ['emailConfirmToken' => $longString]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['emailConfirmToken' => 'The email confirm token field must not be greater than 255 characters']);
    }

    public function testTokenNotFoundFail(): void
    {
        $invalidEmailConfirmToken = '1234567';

        $this->patchJson(route(self::ROUTE_NAME, $invalidEmailConfirmToken))
            ->assertNotFound();
    }
}
