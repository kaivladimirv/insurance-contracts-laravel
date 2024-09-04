<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate\Messages;

abstract class AbstractMessage implements MessageInterface
{
    protected function __construct(
        protected MessageType $type,
        protected string $chatId
    ) {
    }

    public function getType(): MessageType
    {
        return $this->type;
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }
}
