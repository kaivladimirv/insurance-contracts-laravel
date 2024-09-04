<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\ProvidedService;

use App\Events\ProvidedService\RegistrationOfProvidedServiceCanceled;
use App\Listeners\ProvidedServiceEventSubscriber;
use App\Models\Contract;
use App\Models\InsuredPerson;
use App\Models\ProvidedService;
use Database\Factories\ContractServiceFactory;
use Database\Factories\ProvidedServiceFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Override;
use Random\RandomException;
use Tests\TestCase;

class ProvidedServiceDestroyTest extends TestCase
{
    private const string ROUTE_NAME = 'providedServices.destroy';

    private InsuredPerson $insuredPerson;
    private ProvidedService $providedService;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contract = Contract::factory()->for($this->company)->createOne();
        $contractService = ContractServiceFactory::new()->for($contract)->withLimitQuantity(10)->createOne();
        $this->insuredPerson = InsuredPerson::factory()->for($contract)->createOne();

        $this->providedService = ProvidedServiceFactory::new()
            ->for($contractService)
            ->for($this->insuredPerson)
            ->createOne(
                [
                    'date_of_service' => Carbon::parse($contract->start_date)->toDateString(),
                    'quantity' => 1,
                    'price' => 100
                ]
            );
    }

    public function testSuccess(): void
    {
        Event::fake();
        $this->delete(route(self::ROUTE_NAME, [$this->insuredPerson, $this->providedService]))
            ->assertNoContent();

        $this->assertSoftDeleted($this->providedService);
        Event::assertDispatched(RegistrationOfProvidedServiceCanceled::class);
        Event::assertListening(RegistrationOfProvidedServiceCanceled::class, [ProvidedServiceEventSubscriber::class, 'handleCanceled']);
    }

    /**
     * @throws RandomException
     */
    public function testNotFoundFail(): void
    {
        $nonExistentProvidedServiceId = fake()->numberBetween(100);

        $this->deleteJson(route(self::ROUTE_NAME, [$this->insuredPerson, $nonExistentProvidedServiceId]))
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->deleteJson(route(self::ROUTE_NAME, [$this->insuredPerson, $this->providedService]))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->deleteJson(route(self::ROUTE_NAME, [$this->insuredPerson, $this->providedService]))
            ->assertUnauthorized();
    }
}
