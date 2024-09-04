<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\ProvidedService;

use App\Events\ProvidedService\ProvidedServiceRegistered;
use App\Listeners\ProvidedServiceEventSubscriber;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\InsuredPerson;
use App\Models\Person;
use App\Models\ProvidedService;
use App\Models\Service;
use Database\Factories\BalanceFactory;
use Database\Factories\ContractFactory;
use Database\Factories\ContractServiceFactory;
use Database\Factories\InsuredPersonFactory;
use Database\Factories\ProvidedServiceFactory;
use Database\Factories\ServiceFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Override;
use Tests\TestCase;

class ProvidedServiceStoreTest extends TestCase
{
    private const string ROUTE_NAME = 'providedServices.store';

    private ServiceFactory $serviceFactory;
    private ContractFactory $contractFactory;
    private ContractServiceFactory $contractServiceFactory;
    private InsuredPersonFactory $insuredPersonFactory;
    private InsuredPerson $insuredPerson;
    private array $formData;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->serviceFactory = Service::factory()->for($this->company);
        $personFactory = Person::factory()->for($this->company);
        $this->contractFactory = Contract::factory()->for($this->company);
        $this->contractServiceFactory = ContractService::factory()->for($this->serviceFactory);

        $contract = $this->contractFactory->withCurrentYear()->createOne();

        $this->insuredPersonFactory = InsuredPerson::factory()->set('contract_id', $contract)->set('person_id', $personFactory);
        $this->insuredPerson = $this->insuredPersonFactory->createOne();

        $contractService = $this->contractServiceFactory->withLimitQuantity(10)->createOne(['contract_id' => $contract]);
        BalanceFactory::new()->for($this->insuredPerson)->for($contractService)->createOne();

        $this->formData = ProvidedService::factory()
            ->for($this->insuredPerson)
            ->for($contractService)
            ->make(
                [
                    'date_of_service' => Carbon::parse($contract->start_date)->toDateString(),
                    'quantity' => 5,
                    'price' => 1000
                ]
            )
            ->withoutRelations()
            ->toArray();
    }

    public function testSuccess(): void
    {
        Event::fake();

        $this->postJson(route(self::ROUTE_NAME, $this->insuredPerson), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(ProvidedService::class, $this->formData);
        Event::assertDispatched(ProvidedServiceRegistered::class);
        Event::assertListening(ProvidedServiceRegistered::class, [ProvidedServiceEventSubscriber::class, 'handleRegistered']);
    }

    public function testServiceIdRequiredFail(): void
    {
        unset($this->formData['service_id']);

        $this->postJson(route(self::ROUTE_NAME, $this->insuredPerson), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('service_id');
    }

    public function testDateOfServiceRequiredFail(): void
    {
        unset($this->formData['date_of_service']);

        $this->postJson(route(self::ROUTE_NAME, $this->insuredPerson), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('date_of_service');
    }

    public function testQuantityRequiredFail(): void
    {
        unset($this->formData['quantity']);

        $this->postJson(route(self::ROUTE_NAME, $this->insuredPerson), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('quantity');
    }

    public function testPriceRequiredFail(): void
    {
        unset($this->formData['price']);

        $this->postJson(route(self::ROUTE_NAME, $this->insuredPerson), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('price');
    }

    public function testQuantityGreaterThanZeroFail(): void
    {
        $this->formData['quantity'] = 0;

        $this->postJson(route(self::ROUTE_NAME, $this->insuredPerson), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('quantity');
    }

    public function testPriceGreaterThanZeroFail(): void
    {
        $this->formData['price'] = 0;

        $this->postJson(route(self::ROUTE_NAME, $this->insuredPerson), $this->formData)
            ->assertUnprocessable()
            ->assertInvalid('price');
    }

    public function testContractHasExpiredFail(): void
    {
        $contract = $this->contractFactory->withPreviousYear()->createOne();
        $insuredPerson = $this->insuredPersonFactory->set('contract_id', $contract)->withAllowedToExceedLimit()->createOne();
        $contractService = $this->contractServiceFactory->set('contract_id', $contract)->createOne();

        $formData = ProvidedService::factory()
            ->for($insuredPerson)
            ->for($contractService)
            ->make()
            ->toArray();

        $this->postJson(route(self::ROUTE_NAME, $insuredPerson), $formData)
            ->assertBadRequest()
            ->assertJson(['message' => __('The contract has expired')]);
    }

    public function testServiceIsNotCoveredFail(): void
    {
        $serviceIsNotCovered = $this->serviceFactory->createOne();

        $formData = ProvidedService::factory()
            ->for($this->company)
            ->for($this->insuredPerson)
            ->for($serviceIsNotCovered)
            ->make()
            ->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->insuredPerson), $formData)
            ->assertNotFound();
    }

    public function testDateOfServiceIsNotIncludedInContractPeriodFail(): void
    {
        $dateOfService = now()->addYear()->startOfYear()->toDateString();

        $contract = $this->contractFactory->withCurrentYear()->createOne();
        $insuredPerson = $this->insuredPersonFactory->set('contract_id', $contract)->withAllowedToExceedLimit()->createOne();
        $contractService = $this->contractServiceFactory->set('contract_id', $contract)->createOne();

        $formData = ProvidedService::factory()
            ->for($insuredPerson)
            ->for($contractService)
            ->make(['date_of_service' => $dateOfService])
            ->toArray();

        $this->postJson(route(self::ROUTE_NAME, $insuredPerson), $formData)
            ->assertBadRequest()
            ->assertJson(['message' => __('The date of service provision is not included in the contract period')]);
    }

    public function testAmountDoesExceedLimitFail(): void
    {
        $limitValue = 1000;
        $contract = $this->contractFactory->withCurrentYear()->createOne();
        $insuredPerson = $this->insuredPersonFactory->set('contract_id', $contract)->withNotAllowedToExceedLimit()->createOne();
        $contractService = $this->contractServiceFactory->set('contract_id', $contract)->withLimitSum($limitValue)->createOne();

        BalanceFactory::new()->for($insuredPerson)->for($contractService)->createOne();

        $formData = ProvidedServiceFactory::new()
            ->for($insuredPerson)
            ->for($contractService)
            ->make(
                [
                    'date_of_service' => Carbon::parse($contract->start_date)->toDateString(),
                    'quantity' => 20,
                    'price' => 1000
                ]
            )
            ->toArray();

        $this->postJson(route(self::ROUTE_NAME, $insuredPerson), $formData)
            ->assertBadRequest()
            ->assertJson(['message' => __('Service amount does exceed limit', ['limitValue' => $limitValue, 'balance' => $limitValue])]);
    }

    public function testAmountOfServicesProvidedExceedsMaxAmountUnderContractFail(): void
    {
        $contract = $this->contractFactory->withCurrentYear()->createOne(['max_amount' => 1000]);
        $insuredPerson = $this->insuredPersonFactory->set('contract_id', $contract)->withAllowedToExceedLimit()->createOne();
        $contractService = $this->contractServiceFactory->set('contract_id', $contract)->createOne();

        ProvidedServiceFactory::new()
            ->for($insuredPerson)
            ->for($contractService)
            ->createOne([
                'date_of_service' => Carbon::parse($contract->start_date)->toDateString(),
                'quantity' => 1,
                'price' => 1000
            ]);

        $formData = ProvidedService::factory()
            ->for($insuredPerson)
            ->for($contractService)
            ->make([
                'date_of_service' => Carbon::parse($contract->start_date)->toDateString(),
                'quantity' => 2,
                'price' => 1000
            ])
            ->toArray();

        $replace = [
            'maxAmount' => $contract->max_amount,
            'remainder' => 0
        ];

        $this->postJson(route(self::ROUTE_NAME, $insuredPerson), $formData)
            ->assertBadRequest()
            ->assertJson(['message' => __('The amount of services provided exceeds the maximum amount under the contract', $replace)]);
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME, $this->insuredPerson), $this->formData)
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME, $this->insuredPerson), $this->formData)
            ->assertUnauthorized();
    }
}
