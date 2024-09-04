<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate;

use App\Services\Telegram\IncomingUpdate\Exceptions\InvalidMessageData;
use App\Services\Telegram\IncomingUpdate\Exceptions\UnknownMessage;
use App\Services\Telegram\IncomingUpdate\Messages\ChatMemberBanned;
use App\Services\Telegram\IncomingUpdate\Messages\ConfirmationJoinChatbot;
use App\Services\Telegram\IncomingUpdate\Messages\MessageInterface;

class IncomingUpdateConverter
{
    /**
     * @throws UnknownMessage
     */
    public function toMessage(array $incomingUpdate): MessageInterface
    {
        /**
         * @var MessageInterface[] $messageClasses
         */
        $messageClasses = [
            ConfirmationJoinChatbot::class,
            ChatMemberBanned::class
        ];

        foreach ($messageClasses as $messageClass) {
            try {
                return $messageClass::createFromArray($incomingUpdate);
            } catch (InvalidMessageData) {
            }
        }

        throw new UnknownMessage();
    }
}
