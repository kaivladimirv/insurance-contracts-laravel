<?php

declare(strict_types=1);

namespace App\UseCases\Person\LeaveChatbot;

use App\ReadModels\PersonFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class LeaveChatbotHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private PersonFetcher $fetcher)
    {
    }

    public function handle(LeaveChatbotCommand|Command $command): void
    {
        $person = $this->fetcher->getOneByTelegramChaId($command->chatId);

        $person->leaveChatbot();
        $person->save();
    }
}
