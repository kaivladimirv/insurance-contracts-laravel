<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate\Messages;

enum MessageType: int
{
    case NEW_MESSAGE = 1;
    case CONFIRMATION_JOIN_CHATBOT = 2;
    case CHAT_MEMBER_BANNED = 3;
}
