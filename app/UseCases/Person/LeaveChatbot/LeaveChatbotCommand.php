<?php

declare(strict_types=1);

namespace App\UseCases\Person\LeaveChatbot;

use App\UseCases\Command;
use Spatie\LaravelData\Data;

class LeaveChatbotCommand extends Data implements Command
{
    public function __construct(readonly public string $chatId)
    {
    }
}
