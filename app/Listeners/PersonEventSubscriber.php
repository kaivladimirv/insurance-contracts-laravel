<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\NotifierType;
use App\Events\Person\PersonAdded;
use App\Events\Person\PersonUpdated;
use App\Models\Person;
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
        /** @var Person $person */
        $person = Person::query()->findOrFail($event->personId);

        if ($person->notifier_type === NotifierType::TELEGRAM) {
            $this->sendInvitationToJoinChatBot($person);
        }
    }

    /**
     * @psalm-api
     */
    public function handlePersonUpdated(PersonUpdated $event): void
    {
        /** @var Person $person */
        $person = Person::query()->findOrFail($event->personId);

        if (
            ($event->hasNotifierTypeChanged or $event->hasPhoneNumberChanged)
            and ($person->notifier_type === NotifierType::TELEGRAM)
        ) {
            $this->sendInvitationToJoinChatBot($person);
        }
    }

    private function sendInvitationToJoinChatBot(Person $person): void
    {
        try {
            $command = new SendInviteToJoinChatBotCommand($person->id);
            $this->createInvitationToJoinChatBotHandler->handle($command);
        } catch (DomainException $e) {
            Log::error($e->getMessage(), ['person_id' => $person->id]);
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
