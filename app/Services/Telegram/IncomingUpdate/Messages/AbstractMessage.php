<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate\Messages;

use Override;

abstract class AbstractMessage implements MessageInterface
{
    protected function __construct(
        protected MessageType $type,
        protected string $chatId
    ) {
    }

    #[Override]
    public function getType(): MessageType
    {
        return $this->type;
    }

    #[Override]
    public function getChatId(): string
    {
        return $this->chatId;
    }
}
