<?php

declare(strict_types=1);

namespace Tests\Feature\UseCases\Person;

use App\Enums\NotifierType;
use App\UseCases\Person\SendInviteToJoinChatBot\SendInviteToJoinChatBotCommand;
use App\UseCases\Person\SendInviteToJoinChatBot\SendInviteToJoinChatBotHandler;
use Database\Factories\PersonFactory;
use DomainException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class SendInviteToJoinChatBotTest extends TestCase
{
    private PersonFactory $personFactory;
    private SendInviteToJoinChatBotHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->personFactory = PersonFactory::new()->for($this->company);
        $this->handler = App::make(SendInviteToJoinChatBotHandler::class);
    }

    public function testInvitationHasAlreadyBeenSentFail(): void
    {
        $person = $this->personFactory->withInvitationSent()->createOne(['notifier_type' => NotifierType::TELEGRAM]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(__('The invitation has already been sent'));

        $command = new SendInviteToJoinChatBotCommand($person->id);
        $this->handler->handle($command);
    }

    public function testTelegramMustBeSpecifiedAsNotificationChannelFail(): void
    {
        $person = $this->personFactory->createOne(['notifier_type' => NotifierType::EMAIL]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(__('Telegram must be specified as the notification channel'));

        $command = new SendInviteToJoinChatBotCommand($person->id);
        $this->handler->handle($command);
    }

    public function testEmailAddressMustBeSpecifiedFail(): void
    {
        $person = $this->personFactory->createOne(
            [
                'notifier_type' => NotifierType::TELEGRAM,
                'email' => null
            ]
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(__('Email address must be specified'));

        $command = new SendInviteToJoinChatBotCommand($person->id);
        $this->handler->handle($command);
    }
}
