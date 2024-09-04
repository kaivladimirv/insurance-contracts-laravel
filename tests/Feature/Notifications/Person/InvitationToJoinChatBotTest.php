<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications\Person;

use App\Notifications\Person\InvitationToJoinChatBot;
use Database\Factories\PersonFactory;
use DomainException;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class InvitationToJoinChatBotTest extends TestCase
{
    private PersonFactory $personFactory;
    private Notification $notification;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->personFactory = PersonFactory::new()->for($this->company);
        $this->notification = new InvitationToJoinChatBot();
    }

    public function testSuccess(): void
    {
        $person = $this->personFactory->createOne(['telegram_chat_invite_token' => fake()->uuid()]);

        $mailText = $this->notification->toMail($person)->render()->toHtml();

        $expectedString = __('Join InsuranceContract ChatBot on Telegram');

        $this->assertStringContainsString($expectedString, $mailText);
    }

    public function testFail(): void
    {
        $person = $this->personFactory->createOne();

        $this->expectException(DomainException::class);
        $this->notification->toMail($person);
    }
}
