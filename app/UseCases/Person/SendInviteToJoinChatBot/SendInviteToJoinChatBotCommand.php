<?php

declare(strict_types=1);

namespace App\UseCases\Person\SendInviteToJoinChatBot;

use App\UseCases\Command;
use Spatie\LaravelData\Data;

class SendInviteToJoinChatBotCommand extends Data implements Command
{
    public function __construct(readonly public int $person_id)
    {
    }
}
