<?php

declare(strict_types=1);

namespace App\UseCases\Person\JoinToChatbot;

use App\UseCases\Command;
use Spatie\LaravelData\Data;

class JoinToChatbotCommand extends Data implements Command
{
    public function __construct(readonly public string $inviteToken, readonly public string $chatId)
    {
    }
}
