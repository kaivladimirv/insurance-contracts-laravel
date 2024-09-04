<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\TelegramSetting;
use App\Services\Telegram\IncomingUpdate\Exceptions\FailedToReceiveIncomingUpdates;
use App\Services\Telegram\IncomingUpdate\Exceptions\UnknownMessage;
use App\Services\Telegram\IncomingUpdate\MessageHandlers\ChatMemberBannedHandler;
use App\Services\Telegram\IncomingUpdate\MessageHandlers\MessageHandler;
use App\Services\Telegram\IncomingUpdate\MessageHandlers\ConfirmationJoinChatbotHandler;
use App\Services\Telegram\IncomingUpdate\IncomingUpdateConverter;
use App\Services\Telegram\IncomingUpdate\IncomingUpdatesFetcher;
use App\Services\Telegram\IncomingUpdate\Messages\MessageType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessIncomingTelegramUpdates extends Command
{
    private const int LIMIT = 2;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-incoming-telegram-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process incoming Telegram updates';

    /**
     * Execute the console command.
     * @throws FailedToReceiveIncomingUpdates
     */
    public function handle(
        IncomingUpdatesFetcher $incomingUpdatesFetcher,
        IncomingUpdateConverter $incomingUpdateConverter
    ): void {
        $offset = $this->getBotLastUpdateId() + 1;

        foreach ($incomingUpdatesFetcher->fetch(self::LIMIT, $offset) as $incomingUpdate) {
            try {
                $message = $incomingUpdateConverter->toMessage($incomingUpdate);
                $this->getHandler($message->getType())?->handle($message);
            } catch (UnknownMessage $e) {
                Log::notice($e->getMessage());
            }

            $this->saveBotLastUpdateId($incomingUpdate['update_id']);
        }
    }

    private function getHandler(MessageType $messageType): ?MessageHandler
    {
        $className = $this->resolveHandlerClassName($messageType);

        return $className ? app($className) : null;
    }

    /**
     * @return class-string<MessageHandler>|null
     */
    private function resolveHandlerClassName(MessageType $messageType): ?string
    {
        return match ($messageType) {
            MessageType::CONFIRMATION_JOIN_CHATBOT => ConfirmationJoinChatbotHandler::class,
            MessageType::CHAT_MEMBER_BANNED => ChatMemberBannedHandler::class,
            default => null
        };
    }

    private function getBotLastUpdateId(): int
    {
        return intval(TelegramSetting::query()->value('bot_last_update_id') ?: 0);
    }

    private function saveBotLastUpdateId(int $id): void
    {
        TelegramSetting::query()->firstOrNew()
            ->fill(['bot_last_update_id' => $id])
            ->save();
    }
}
