<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\Models\Person;
use DomainException;

class InvitationLinkToJoinChatbot
{
    private const string PREFIX_MESSAGE = 'start=';

    private string $inviteToken;

    private function __construct()
    {
    }

    public static function createFromPerson(Person $person): self
    {
        if (!$person->telegram_chat_invite_token) {
            throw new DomainException(__('An invitation token has not been generated for person', ['person' => $person->getFullName()]));
        }

        $self = new self();
        $self->inviteToken = $person->telegram_chat_invite_token;

        return $self;
    }

    public function __toString(): string
    {
        return config('services.telegram-bot-api.url') . '?' . self::PREFIX_MESSAGE . $this->inviteToken;
    }
}
