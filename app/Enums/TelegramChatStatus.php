<?php

declare(strict_types=1);

namespace App\Enums;

enum TelegramChatStatus: int
{
    case INVITATION_SENT = 0;
    case JOINED = 1;
    case LEAVE = 2;
}
