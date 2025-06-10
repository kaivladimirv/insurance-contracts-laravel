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
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[DataProvider('invalidDataProvider')]
    public function testInvalidData(string $field, mixed $invalidValue, string $expectedMessage): void
    {
        $this->formData[$field] = $invalidValue;

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $expectedMessage]);
    }

    public static function invalidDataProvider(): array
    {
        $longString = fake()->regexify('[A-Za-z0-9]{300}');

        return [
            ['name', null, 'The name field is required'],
            ['name', $longString, 'The name field must not be greater than 255 characters'],
            ['email', null, 'The email field is required'],
            ['email', $longString, 'The email field must not be greater than 255 characters'],
            ['password', Str::password(7), 'The password field must be at least 8 characters'],
            ['password', $longString, 'The password field must not be greater than 255 characters'],
            ['password', Str::password(8, letters: false), 'The password field must contain at least one letter'],
            ['password', Str::password(8, symbols: false), 'The password field must contain at least one symbol'],
            ['password', Str::password(8, numbers: false), 'The password field must contain at least one number'],
            ['password', strtolower(Str::password(8)), 'The password field must contain at least one uppercase and one lowercase letter'],
            ['password', 'P@ssw0rd', 'The given password has appeared in a data leak. Please choose a different password.'],
            ['password', 'P@ssw0rd', 'The given password has appeared in a data leak. Please choose a different password.']
        ];
    }

    public function testPasswordMismatchFail(): void
    {
        $this->formData['password_confirmation'] = $this->formData['password_confirmation'] . 'ABC';

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => 'The password field confirmation does not match']);
    }

    #[DataProvider('uniqueFieldsProvider')]
    public function testUniqueFields(string $field, string $expectedMessage): void
    {
        Company::factory()->createOne([$field => $this->company->getAttribute($field)]);

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $expectedMessage]);
    }

    public static function uniqueFieldsProvider(): array
    {
        return [
            ['name', 'The name has already been taken'],
            ['email', 'The email has already been taken'],
        ];
    }
}
