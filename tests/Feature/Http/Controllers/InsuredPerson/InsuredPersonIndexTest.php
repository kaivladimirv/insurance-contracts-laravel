<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\InsuredPerson;

use App\Models\Contract;
use App\Models\InsuredPerson;
use Database\Factories\InsuredPersonFactory;
use Illuminate\Support\Str;
use Override;
use Tests\TestCase;

class InsuredPersonIndexTest extends TestCase
{
    private const string ROUTE_NAME = 'insuredPerson.index';

    private Contract $contract;
    private InsuredPersonFactory $insuredPersonFactory;


    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->contract = Contract::factory()->for($this->company)->createOne();
        $this->insuredPersonFactory = InsuredPerson::factory()->for($this->contract);
    }

    public function testPolicyNumberSuccess(): void
    {
        $policyNumber = fake()->unique()->regexify('[A-Za-z0-9]{20}');

        $this->insuredPersonFactory->count(30)->create();
        $this->insuredPersonFactory->count(1)->create(['policy_number' => $policyNumber]);

        $params = [
            'page' => 1,
            'policy_number' => $policyNumber
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->contract, ...$params]))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function testIsAllowedToExceedLimitSuccess(): void
    {
        $this->insuredPersonFactory->count(3)->withNotAllowedToExceedLimit()->create();
        $this->insuredPersonFactory->count(2)->withAllowedToExceedLimit()->create();

        $params = [
            'page' => 1,
            'is_allowed_to_exceed_limit' => true
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->contract, ...$params]))
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME, $this->contract))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME, $this->contract))
            ->assertUnauthorized();
    }
}
