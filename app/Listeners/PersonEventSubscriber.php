<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\NotifierType;
use App\Events\Person\PersonAdded;
use App\Events\Person\PersonUpdated;
use App\UseCases\Person\SendInviteToJoinChatBot\SendInviteToJoinChatBotCommand;
use App\UseCases\Person\SendInviteToJoinChatBot\SendInviteToJoinChatBotHandler;
use DomainException;
use Illuminate\Support\Facades\Log;

readonly class PersonEventSubscriber
{
    public function __construct(
        private SendInviteToJoinChatBotHandler $createInvitationToJoinChatBotHandler
    ) {
    }

    /**
     * @psalm-api
     */
    public function handlePersonAdded(PersonAdded $event): void
    {
        if ($event->notifierType === NotifierType::TELEGRAM) {
            $this->sendInvitationToJoinChatBot($event->personId);
        }
    }

    /**
     * @psalm-api
     */
    public function handlePersonUpdated(PersonUpdated $event): void
    {
        if (
            ($event->hasNotifierTypeChanged or $event->hasPhoneNumberChanged)
            and ($event->notifierType === NotifierType::TELEGRAM)
        ) {
            $this->sendInvitationToJoinChatBot($event->personId);
        }
    }

    private function sendInvitationToJoinChatBot(int $personId): void
    {
        try {
            $command = new SendInviteToJoinChatBotCommand($personId);
            $this->createInvitationToJoinChatBotHandler->handle($command);
        } catch (DomainException $e) {
            Log::error($e->getMessage(), ['person_id' => $personId]);
        }
    }

    public function subscribe(): array
    {
        return [
            PersonAdded::class => 'handlePersonAdded',
            PersonUpdated::class => 'handlePersonUpdated'
        ];
    }
}
