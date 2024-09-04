<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Override;
use Tests\TestCase;

class CompanyTokenTest extends TestCase
{
    private const string ROUTE_NAME = 'company.token';

    private string $password;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->password = Str::password();
        $this->company = Company::factory()->createOne(['password_hash' => Hash::make($this->password)]);
    }

    public function testSuccess(): void
    {
        $oldAccessToken  = $this->company->createAccessToken()->plainTextToken;

        $this->withBasicAuth($this->company->email, $this->password)
            ->patch(route(self::ROUTE_NAME))
            ->assertOk()
            ->assertJsonStructure(['token_type', 'access_token']);

        $this->company->refresh();

        $this->assertEquals(1, $this->company->tokens()->count());
        $this->assertNull(PersonalAccessToken::findToken($oldAccessToken));
    }

    public function testGuestFail(): void
    {
        $this->patch(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }

    public function testBasicAuthFail(): void
    {
        $invalidPassword = '123';

        $this->withBasicAuth($this->company->email, $invalidPassword)
            ->patch(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }
}
