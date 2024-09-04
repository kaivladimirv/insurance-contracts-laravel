<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use App\Events\Company\CompanyRegistered;
use App\Models\Company;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class CompanyRegisterTest extends TestCase
{
    private const string ROUTE_NAME = 'company.register';

    private array $formData;

    private string $password;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->make();
        $this->password = Str::password();
        $this->formData = $this->convertCompanyToFormData($this->company);
    }

    private function convertCompanyToFormData(Company $company): array
    {
        $formData = Arr::except($company->toArray(), 'password_hash');

        $formData['password'] = $this->password;
        $formData['password_confirmation'] = $formData['password'];

        return $formData;
    }

    public function testSuccess(): void
    {
        Event::fake();

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertNoContent();

        $expected = Arr::except($this->formData, ['password', 'password_confirmation']);

        $this->assertDatabaseHas(Company::class, $expected);
        $this->assertTrue(
            Hash::check($this->password, Company::query()->firstWhere('email', $this->formData['email'])->password_hash)
        );
        Event::assertDispatched(CompanyRegistered::class);
    }

    public function testNameRequiredFail(): void
    {
        $this->formData['name'] = '';

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name' => 'The name field is required']);
    }

    public function testNameMax255Fail(): void
    {
        $this->formData['name'] = Str::random(256);

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name' => 'The name field must not be greater than 255 characters']);
    }

    public function testEmailRequiredFail(): void
    {
        $this->formData['email'] = '';

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => 'The email field is required']);
    }

    public function testEmailMax255Fail(): void
    {
        $this->formData['email'] = Str::random(256) . $this->formData['email'];

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => 'The email field must not be greater than 255 characters']);
    }

    public function testPasswordMin8Fail(): void
    {
        $this->formData['password'] = Str::password(7);
        $this->formData['password_confirmation'] = $this->formData['password'];

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => 'The password field must be at least 8 characters']);
    }

    public function testPasswordMax255Fail(): void
    {
        $this->formData['password'] = Str::password(256);
        $this->formData['password_confirmation'] = $this->formData['password'];

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => 'The password field must not be greater than 255 characters']);
    }

    public function testPasswordLettersFail(): void
    {
        $this->formData['password'] = Str::password(8, letters: false);
        $this->formData['password_confirmation'] = $this->formData['password'];

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => 'The password field must contain at least one letter']);
    }

    public function testPasswordSymbolsFail(): void
    {
        $this->formData['password'] = Str::password(8, symbols: false);
        $this->formData['password_confirmation'] = $this->formData['password'];

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => 'The password field must contain at least one symbol']);
    }

    public function testPasswordNumbersFail(): void
    {
        $this->formData['password'] = Str::password(8, numbers: false);
        $this->formData['password_confirmation'] = $this->formData['password'];

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => 'The password field must contain at least one number']);
    }

    public function testPasswordMixedCaseFail(): void
    {
        $this->formData['password'] = strtolower(Str::password(8));
        $this->formData['password_confirmation'] = $this->formData['password'];

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => 'The password field must contain at least one uppercase and one lowercase letter']);
    }

    public function testPasswordUncompromisedFail(): void
    {
        $this->formData['password'] = 'P@ssw0rd';
        $this->formData['password_confirmation'] = $this->formData['password'];

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => 'The given password has appeared in a data leak. Please choose a different password.']);
    }

    public function testPasswordMismatchFail(): void
    {
        $this->formData['password_confirmation'] = $this->formData['password_confirmation'] . 'ABC';

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => 'The password field confirmation does not match']);
    }

    public function testNameUniqueFail(): void
    {
        Company::factory()->createOne(['name' => $this->company->name]);

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name' => 'The name has already been taken']);
    }

    public function testEmailUniqueFail(): void
    {
        Company::factory()->createOne(['email' => $this->company->email]);

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => 'The email has already been taken']);
    }
}
