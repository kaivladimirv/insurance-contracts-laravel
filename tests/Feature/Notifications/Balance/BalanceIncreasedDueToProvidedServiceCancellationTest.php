<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications\Balance;

use App\Notifications\Balance\BalanceIncreasedDueToProvidedServiceCancellation;
use Database\Factories\BalanceFactory;
use Database\Factories\ContractServiceFactory;
use Database\Factories\InsuredPersonFactory;
use Database\Factories\ProvidedServiceFactory;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class BalanceIncreasedDueToProvidedServiceCancellationTest extends TestCase
{
    private Notification $notification;

    protected function setUp(): void
    {
        parent::setUp();

        $contractService = ContractServiceFactory::new()->createOne();
        $insuredPerson = InsuredPersonFactory::new()->createOne();
        $providedService = ProvidedServiceFactory::new()->for($insuredPerson)->for($contractService)->createOne();
        $balance = BalanceFactory::new()->for($insuredPerson)->for($contractService)->createOne();

        $this->notification = new BalanceIncreasedDueToProvidedServiceCancellation($balance, $providedService);
    }

    public function testSuccess(): void
    {
        $mailText = $this->notification->toMail()->render()->toHtml();
        $telegramMessageText = implode(PHP_EOL, $this->notification->toTelegram()->toArray());

        $expectedString = __('The service provided was canceled');

        $this->assertStringContainsString($expectedString, $mailText);
        $this->assertStringContainsString($expectedString, $telegramMessageText);
    }
}
