<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate\Messages;

use App\Services\Telegram\IncomingUpdate\Exceptions\InvalidMessageData;

interface MessageInterface
{
    /**
     * @throws InvalidMessageData
     */
    public static function createFromArray(array $incomingUpdate): self;

    public function getType(): ?MessageType;

    public function getChatId(): string;
}
