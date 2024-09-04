<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\ProvidedService;

use App\Enums\LimitType;
use App\Models\Contract;
use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use App\Models\Service;
use Database\Factories\ProvidedServiceFactory;
use Override;
use Tests\TestCase;

class ProvidedServiceIndexTest extends TestCase
{
    private const string ROUTE_NAME = 'providedServices.index';

    private InsuredPerson $insuredPerson;
    private ProvidedServiceFactory $providedServiceFactory;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contract = Contract::factory()->for($this->company)->createOne();
        $this->insuredPerson = InsuredPerson::factory()->for($contract)->createOne();
        $this->providedServiceFactory = ProvidedService::factory()
            ->for($this->company)
            ->for($contract)
            ->for(Service::factory()->for($this->company))
            ->for(InsuredPerson::factory()->for($contract));
    }

    public function testLimitTypeAndQuantitySuccess(): void
    {
        $searchByQuantityFrom = 6;
        $searchByQuantityTo = 10;
        $expectedRecordCount = 2;

        $this->providedServiceFactory->createMany(3);

        $this->providedServiceFactory->for($this->insuredPerson)
            ->withQuantityBetween(1, 5)
            ->state(['limit_type' => LimitType::QUANTITY])
            ->createMany(5);

        $this->providedServiceFactory->for($this->insuredPerson)
            ->withQuantityBetween($searchByQuantityFrom, $searchByQuantityTo)
            ->state(['limit_type' => LimitType::QUANTITY])
            ->createMany($expectedRecordCount);

        $params = [
            'page' => 1,
            'limit_type' => LimitType::QUANTITY,
            'quantity_from' => $searchByQuantityFrom,
            'quantity_to' => $searchByQuantityTo
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->insuredPerson, ...$params]))
            ->assertOk()
            ->assertJsonCount($expectedRecordCount, 'data');
    }

    public function testServiceIdSuccess(): void
    {
        $service = Service::factory()->createOne();

        $this->providedServiceFactory->for($service)->createMany(10);
        $this->providedServiceFactory->for($service)->for($this->insuredPerson)->createMany(2);

        $params = [
            'page' => 1,
            'service_id' => $service->id
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->insuredPerson, ...$params]))
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testServiceNameAndPriceSuccess(): void
    {
        $service = Service::factory()->createOne();

        $this->providedServiceFactory = $this->providedServiceFactory->for($service)
            ->for($this->insuredPerson)
            ->set('service_name', $service->name);

        $this->providedServiceFactory->state(['price' => 100])->createMany(10);
        $this->providedServiceFactory->state(['price' => 200])->createMany(3);

        $params = [
            'page' => 1,
            'service_name' => $service->name,
            'price_from' => 200,
            'price_to' => 200,
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->insuredPerson, ...$params]))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testAmountFromSuccess(): void
    {
        $service = Service::factory()->createOne();

        $this->providedServiceFactory = $this->providedServiceFactory->for($service)
            ->for($this->insuredPerson);

        $this->providedServiceFactory->state(['quantity' => 1, 'price' => '300'])->createMany(10);
        $this->providedServiceFactory->state(['quantity' => 2, 'price' => '100'])->createMany(2);

        $params = [
            'page' => 1,
            'amount_from' => 300
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->insuredPerson, ...$params]))
            ->assertOk()
            ->assertJsonCount(10, 'data');
    }

    public function testAmountToSuccess(): void
    {
        $quantity = 1;
        $price = 300;
        $searchByAmountTo = $quantity * $price;
        $expectedRecordCount = 3;

        $service = Service::factory()->createOne();

        $this->providedServiceFactory = $this->providedServiceFactory->for($service)
            ->for($this->insuredPerson);

        $this->providedServiceFactory->state(['quantity' => 1, 'price' => '300'])->createMany($expectedRecordCount);
        $this->providedServiceFactory->state(['quantity' => 2, 'price' => '500'])->createMany(2);

        $params = [
            'page' => 1,
            'amount_to' => $searchByAmountTo
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->insuredPerson, ...$params]))
            ->assertOk()
            ->assertJsonCount($expectedRecordCount, 'data');
    }

    public function testPeriodSuccess(): void
    {
        $service = Service::factory()->createOne();

        $this->providedServiceFactory = $this->providedServiceFactory->for($service)
            ->for($this->insuredPerson);

        $this->providedServiceFactory->withDateOfServiceBetween('2023-01-01', '2023-01-31')->createMany(4);
        $this->providedServiceFactory->withDateOfServiceBetween('2023-02-01', '2023-02-05')->createMany(3);


        $params = [
            'page' => 1,
            'date_of_service_from' => '2023-02-01',
            'date_of_service_to' => '2023-03-01'
        ];

        $this->getJson(route(self::ROUTE_NAME, [$this->insuredPerson, ...$params]))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->getJson(route(self::ROUTE_NAME, $this->insuredPerson))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->getJson(route(self::ROUTE_NAME, $this->insuredPerson))
            ->assertUnauthorized();
    }
}
