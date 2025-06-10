<?php

declare(strict_types=1);

namespace App\UseCases\Person\LeaveChatbot;

use App\ReadModels\PersonFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class LeaveChatbotHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private PersonFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(LeaveChatbotCommand|Command $command): void
    {
        $person = $this->fetcher->getOneByTelegramChaId($command->chatId);

        $person->leaveChatbot();
        $person->save();
    }
}
