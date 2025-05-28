<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications\Balance;

use App\Models\Contract;
use Override;
use App\Models\ProvidedService;
use App\Notifications\Balance\BalanceDecreasedDueToProvidedService;
use Database\Factories\BalanceFactory;
use Database\Factories\ContractServiceFactory;
use Database\Factories\InsuredPersonFactory;
use Database\Factories\ProvidedServiceFactory;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class BalanceDecreaseDueToProvidedServiceTest extends TestCase
{
    private ProvidedService $providedService;
    private Notification $notification;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $contract = Contract::factory()->for($this->company)->createOne();
        $contractService = ContractServiceFactory::new()->for($contract)->createOne();
        $insuredPerson = InsuredPersonFactory::new()->for($contract)->createOne();
        $this->providedService = ProvidedServiceFactory::new()->for($insuredPerson)->for($contractService)->createOne();
        $balance = BalanceFactory::new()->for($insuredPerson)->for($contractService)->createOne();

        $this->notification = new BalanceDecreasedDueToProvidedService($balance->balance, $this->providedService->toDto());
    }

    public function testSuccess(): void
    {
        $mailText = $this->notification->toMail()->render()->toHtml();
        $telegramMessageText = implode(PHP_EOL, $this->notification->toTelegram()->toArray());

        $expectedString = __('On :date, the service was provided to you', ['date' => $this->providedService->date_of_service->isoFormat('D MMMM YYYY')]);

        $this->assertStringContainsString($expectedString, $mailText);
        $this->assertStringContainsString($expectedString, $telegramMessageText);
    }
}
