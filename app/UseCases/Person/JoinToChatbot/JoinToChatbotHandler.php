<?php

declare(strict_types=1);

namespace App\UseCases\Person\JoinToChatbot;

use App\ReadModels\PersonFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class JoinToChatbotHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private PersonFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(JoinToChatbotCommand|Command $command): void
    {
        $person = $this->fetcher->getOneByInviteToken($command->inviteToken);

        $person->joinToChatbot($command->chatId);
        $person->save();
    }
}
