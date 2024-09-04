<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use App\Events\Company\CompanyPasswordChanged;
use App\Models\Company;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class CompanyChangePasswordTest extends TestCase
{
    private const string ROUTE_NAME = 'company.changePassword';

    private string $oldPassword;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->oldPassword = Str::password();
        $this->company = Company::factory()->createOne(['password_hash' => Hash::make($this->oldPassword)]);
        $this->company->createAccessToken();
    }

    public function testSuccess(): void
    {
        Event::fake();
        $newPassword = Str::password();

        $formData = [
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ];

        $this->withBasicAuth($this->company->email, $this->oldPassword)
            ->postJson(route(self::ROUTE_NAME), $formData)
            ->assertNoContent();

        $this->company->refresh();

        $this->assertTrue(Hash::check($newPassword, $this->company->password_hash));
        Event::assertDispatched(CompanyPasswordChanged::class);
    }

    public function testPasswordMismatchFail(): void
    {
        $newPassword = Str::password();

        $formData = [
            'password' => $newPassword,
            'password_confirmation' => $newPassword . 'ABC'
        ];

        $this->withBasicAuth($this->company->email, $this->oldPassword)
            ->postJson(route(self::ROUTE_NAME), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => 'The password field confirmation does not match.']);
    }

    public function testOldAndNewPasswordsMatchFail(): void
    {
        $formData = [
            'password' => $this->oldPassword,
            'password_confirmation' => $this->oldPassword
        ];

        $this->withBasicAuth($this->company->email, $this->oldPassword)
            ->postJson(route(self::ROUTE_NAME), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => __('New and old passwords must not match')]);
    }

    public function testGuestFail(): void
    {
        $this->postJson(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }

    public function testBasicAuthFail(): void
    {
        $invalidPassword = '123';

        $this->withBasicAuth($this->company->email, $invalidPassword)
            ->postJson(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }
}
