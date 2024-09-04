<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use App\Events\Company\CompanyEmailChanged;
use App\Models\Company;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class CompanyChangeEmailTest extends TestCase
{
    private const string ROUTE_NAME = 'company.changeEmail';
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
        Event::fake();
        $formData = ['email' => fake()->unique()->freeEmail()];

        $this->withBasicAuth($this->company->email, $this->password)
            ->postJson(route(self::ROUTE_NAME), $formData)
            ->assertNoContent();

        $this->company->refresh();

        $this->assertEquals($formData['email'], $this->company->new_email);
        $this->assertNotNull($this->company->new_email_confirm_token);
        Event::assertDispatched(CompanyEmailChanged::class);
    }

    public function testEmailUniqueFail(): void
    {
        $otherCompany = Company::factory()->createOne();

        $formData = ['email' => $otherCompany->email];

        $this->withBasicAuth($this->company->email, $this->password)
            ->postJson(route(self::ROUTE_NAME), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => __('Email already in use')]);
    }

    public function testEmailDoesMatchedFail(): void
    {
        Event::fake();
        $formData = ['email' => $this->company->email];

        $this->withBasicAuth($this->company->email, $this->password)
            ->postJson(route(self::ROUTE_NAME), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => __('New and old emails must not match')]);
    }

    public function testGuestFail(): void
    {
        $this->postJson(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }

    public function testBasicAuthFail(): void
    {
        $invalidPassword = fake()->unique()->password();

        $this->withBasicAuth($this->company->email, $invalidPassword)
            ->postJson(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }
}
