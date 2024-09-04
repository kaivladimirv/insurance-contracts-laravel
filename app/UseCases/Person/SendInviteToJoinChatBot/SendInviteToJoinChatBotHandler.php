<?php

declare(strict_types=1);

namespace App\UseCases\Person\SendInviteToJoinChatBot;

use App\Enums\NotifierType;
use App\Models\Person;
use App\Notifications\Person\InvitationToJoinChatBot;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use DomainException;
use Illuminate\Support\Str;

readonly class SendInviteToJoinChatBotHandler implements CommandHandler
{
    public function handle(SendInviteToJoinChatBotCommand|Command $command): void
    {
        /** @var Person $person */
        $person = Person::query()->findOrFail($command->person_id);

        $this->assertInviteNotSent($person);
        $this->assertNotifierTypeIsTelegram($person);
        $this->assertEmailSpecified($person);

        $person->telegram_chat_invite_token = Str::uuid()->toString();
        $person->save();

        $person->notify(new InvitationToJoinChatBot());
    }

    private function assertInviteNotSent(Person $person): void
    {
        if ($person->inviteToJoinChatBotHasBeenSent()) {
            throw new DomainException(__('The invitation has already been sent'));
        }
    }

    private function assertNotifierTypeIsTelegram(Person $person): void
    {
        if ($person->notifier_type !== NotifierType::TELEGRAM) {
            throw new DomainException(__('Telegram must be specified as the notification channel'));
        }
    }

    private function assertEmailSpecified(Person $person): void
    {
        if (empty($person->email)) {
            throw new DomainException(__('Email address must be specified'));
        }
    }
}
