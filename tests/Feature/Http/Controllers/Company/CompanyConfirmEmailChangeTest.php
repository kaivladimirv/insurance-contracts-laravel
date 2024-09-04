<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class CompanyConfirmEmailChangeTest extends TestCase
{
    private const string ROUTE_NAME = 'company.confirmEmailChange';

    private string $password;
    private string $email;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->password = Str::password();
        $this->email = fake()->unique()->freeEmail();
        $this->company = Company::factory()->createOne(
            [
                'password_hash' => Hash::make($this->password),
                'new_email' => $this->email,
                'new_email_confirm_token' => Str::uuid()->toString()
            ]
        );
    }

    public function testSuccess(): void
    {
        $this->withBasicAuth($this->company->email, $this->password)
            ->patch(route(self::ROUTE_NAME, $this->company->new_email_confirm_token))
            ->assertNoContent();

        $this->company->refresh();

        $this->assertNull($this->company->new_email);
        $this->assertNull($this->company->new_email_confirm_token);
        $this->assertEquals($this->email, $this->company->email);
        $this->assertFalse($this->company->tokens()->exists());
    }

    public function testTokenNotFoundFail(): void
    {
        $invalidNewEmailConfirmToken = '1234567';

        $this->withBasicAuth($this->company->email, $this->password)
            ->patch(route(self::ROUTE_NAME, $invalidNewEmailConfirmToken))
            ->assertNotFound();
    }

    public function testGuestFail(): void
    {
        $this->patch(route(self::ROUTE_NAME, $this->company->new_email_confirm_token))
            ->assertUnauthorized();
    }

    public function testBasicAuthFail(): void
    {
        $invalidPassword = '123';

        $this->withBasicAuth($this->company->email, $invalidPassword)
            ->patch(route(self::ROUTE_NAME, $this->company->new_email_confirm_token))
            ->assertUnauthorized();
    }
}
