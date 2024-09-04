<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Contract;

use App\Models\Contract;
use Override;
use Tests\TestCase;

class ContractIndexTest extends TestCase
{
    private const string ROUTE_NAME = 'contracts.index';

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        Contract::factory()->for($this->company)
            ->count(33)
            ->create(
                [
                    'start_date' => fn() => fake()->dateTimeBetween('01.01.2024', '31.01.2024')->format('Y-m-d'),
                    'end_date' => fn() => fake()->dateTimeBetween('01.02.2024', '28.02.2024')->format('Y-m-d'),
                    'max_amount' => fn() => fake()->numberBetween(1, 100500)
                ]
            );
    }

    public function testByNumberAndMaxAmountSuccess(): void
    {
        $expectedCount = 2;
        $maxAmountFrom = 200000;
        $maxAmountTo = 300000;

        Contract::factory()->for($this->company)
            ->state([
                'number' => fn() => 'test_' . fake()->randomNumber(),
                'max_amount' => fn() => fake()->numberBetween($maxAmountFrom, $maxAmountTo),
            ])
            ->count($expectedCount)
            ->create();

        $params = [
            'page' => 1,
            'number' => 'test_',
            'max_amount_from' => $maxAmountFrom,
            'max_amount_to' => $maxAmountTo
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount($expectedCount, 'data');
    }

    public function testByPeriodSuccess(): void
    {
        $expectedCount = 2;

        Contract::factory()->for($this->company)
            ->state([
                'start_date' => fn() => fake()->dateTimeBetween('01.03.2024', '31.03.2024')->format('Y-m-d'),
                'end_date' => fn() => fake()->dateTimeBetween('01.04.2024', '30.04.2024')->format('Y-m-d'),
            ])
            ->count($expectedCount)
            ->create();

        $params = [
            'page' => 1,
            'period_from' => '2024-03-01',
            'period_to' => '2024-04-30'
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount($expectedCount, 'data');
    }

    public function testByPeriodFromSuccess(): void
    {
        $expectedCount = 4;

        Contract::factory()->for($this->company)
            ->state([
                'start_date' => fn() => fake()->dateTimeBetween('01.03.2024', '31.03.2024')->format('Y-m-d'),
                'end_date' => fn() => fake()->dateTimeBetween('01.04.2024', '30.04.2024')->format('Y-m-d'),
            ])
            ->count($expectedCount)
            ->create();

        $params = [
            'page' => 1,
            'period_from' => '2024-03-01'
        ];

        $this->getJson(route(self::ROUTE_NAME, $params))
            ->assertOk()
            ->assertJsonCount($expectedCount, 'data');
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
