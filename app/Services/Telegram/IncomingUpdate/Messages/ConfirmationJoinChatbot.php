<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate\Messages;

use App\Services\Telegram\IncomingUpdate\Exceptions\InvalidMessageData;
use Illuminate\Support\Str;

class ConfirmationJoinChatbot extends AbstractMessage
{
    private const string PREFIX_MESSAGE = '/start ';

    private string $inviteToken;

    /**
     * @throws InvalidMessageData
     */
    public static function createFromArray(array $incomingUpdate): self
    {
        if (!self::is($incomingUpdate)) {
            throw new InvalidMessageData(__('This message is not an invitation to join the chatbot'));
        }

        $self = new self(
            MessageType::CONFIRMATION_JOIN_CHATBOT,
            (string)$incomingUpdate['message']['chat']['id']
        );

        $self->inviteToken = self::extractInviteToken($incomingUpdate);

        return $self;
    }

    /**
     * @psalm-api
     */
    public function getToken(): string
    {
        return $this->inviteToken;
    }

    private static function is(array $incomingUpdate): bool
    {
        return Str::startsWith(self::extractTextMessageFrom($incomingUpdate), self::PREFIX_MESSAGE);
    }

    private static function extractInviteToken(array $incomingUpdate): string
    {
        return Str::of(self::extractTextMessageFrom($incomingUpdate))->remove(self::PREFIX_MESSAGE)->value();
    }

    private static function extractTextMessageFrom(array $incomingUpdate): string
    {
        return $incomingUpdate['message']['text'] ?? '';
    }
}
