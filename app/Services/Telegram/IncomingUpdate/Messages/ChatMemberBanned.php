<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate\Messages;

use App\Services\Telegram\IncomingUpdate\Exceptions\InvalidMessageData;

class ChatMemberBanned extends AbstractMessage
{
    /**
     * @throws InvalidMessageData
     */
    public static function createFromArray(array $incomingUpdate): self
    {
        if (!self::is($incomingUpdate)) {
            throw new InvalidMessageData(__('This message is not an ChatMemberBanned'));
        }

        return new self(
            MessageType::CHAT_MEMBER_BANNED,
            (string)$incomingUpdate['my_chat_member']['chat']['id']
        );
    }

    private static function is(array $incomingUpdate): bool
    {
        $status = $incomingUpdate['my_chat_member']['new_chat_member']['status'] ?? null;

        return ($status === 'kicked');
    }
}
