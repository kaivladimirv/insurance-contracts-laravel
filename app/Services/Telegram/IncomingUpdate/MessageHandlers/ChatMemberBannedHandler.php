<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate\MessageHandlers;

use App\Services\Telegram\IncomingUpdate\Messages\ChatMemberBanned;
use App\Services\Telegram\IncomingUpdate\Messages\MessageInterface;
use App\UseCases\Person\LeaveChatbot\LeaveChatbotCommand;
use App\UseCases\Person\LeaveChatbot\LeaveChatbotHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

readonly class ChatMemberBannedHandler implements MessageHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private LeaveChatbotHandler $leaveChatbotHandler)
    {
    }

    public function handle(MessageInterface|ChatMemberBanned $message): void
    {
        try {
            $command = new LeaveChatbotCommand($message->getChatId());
            $this->leaveChatbotHandler->handle($command);
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage(), ['chatId' => $message->getChatId()]);
        }
    }
}
