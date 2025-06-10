<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use App\Models\Company;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CompanyUpdateTest extends TestCase
{
    private const string ROUTE_NAME = 'company.update';

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();
    }

    public function testSuccess(): void
    {
        $formData = Company::factory()->makeOne()->only(['name']);

        $this->postJson(route(self::ROUTE_NAME), $formData)
            ->assertNoContent();

        $expected = array_merge(['id' => $this->company->id], $formData);
        $this->assertDatabaseHas(Company::class, $expected);
    }

    #[DataProvider('invalidDataProvider')]
    public function testInvalidData(string $field, mixed $invalidValue, string $expectedMessage): void
    {
        $formData[$field] = $invalidValue;

        $this->postJson(route(self::ROUTE_NAME), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $expectedMessage]);
    }

    public static function invalidDataProvider(): array
    {
        $longString = fake()->regexify('[A-Za-z0-9]{300}');

        return [
            ['name', null, 'The name field is required'],
            ['name', $longString, 'The name field must not be greater than 255 characters'],
        ];
    }

    public function testNameUniqueFail(): void
    {
        $otherCompany = Company::factory()->createOne();

        $formData = ['name' => $otherCompany->name];

        $this->postJson(route(self::ROUTE_NAME), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name' => 'Name already in use']);
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME))
            ->assertUnauthorized();
    }
}
