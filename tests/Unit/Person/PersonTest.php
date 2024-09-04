<?php

declare(strict_types=1);

namespace Tests\Unit\Person;

use Database\Factories\PersonFactory;
use DomainException;
use Tests\TestCase;

class PersonTest extends TestCase
{
    public function testInviteTokeToJoinChatbotIsNotInstalledFail(): void
    {
        $personWithoutInviteToken = PersonFactory::new()->makeOne(['telegram_chat_invite_token' => null]);

        $this->expectException(DomainException::class);
        $personWithoutInviteToken->markThatInviteToJoinChatBotHasBeenSent(now());
    }
}
