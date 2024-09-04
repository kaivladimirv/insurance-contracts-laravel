<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate\MessageHandlers;

use App\Services\Telegram\IncomingUpdate\Messages\MessageInterface;

interface MessageHandler
{
    public function handle(MessageInterface $message): void;
}
