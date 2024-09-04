<?php

declare(strict_types=1);

namespace App\UseCases\Person\JoinToChatbot;

use App\ReadModels\PersonFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class JoinToChatbotHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private PersonFetcher $fetcher)
    {
    }

    public function handle(JoinToChatbotCommand|Command $command): void
    {
        $person = $this->fetcher->getOneByInviteToken($command->inviteToken);

        $person->joinToChatbot($command->chatId);
        $person->save();
    }
}
