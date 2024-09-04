<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate\MessageHandlers;

use App\Services\Telegram\IncomingUpdate\Messages\ConfirmationJoinChatbot;
use App\Services\Telegram\IncomingUpdate\Messages\MessageInterface;
use App\UseCases\Person\JoinToChatbot\JoinToChatbotCommand;
use App\UseCases\Person\JoinToChatbot\JoinToChatbotHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

readonly class ConfirmationJoinChatbotHandler implements MessageHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private JoinToChatbotHandler $joinToChatbotHandler)
    {
    }

    public function handle(MessageInterface|ConfirmationJoinChatbot $message): void
    {
        try {
            $command = new JoinToChatbotCommand($message->getToken(), $message->getChatId());
            $this->joinToChatbotHandler->handle($command);
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage(), ['inviteToken' => $message->getToken()]);
        }
    }
}
