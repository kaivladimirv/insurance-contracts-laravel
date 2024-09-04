<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\ProcessIncomingTelegramUpdates;
use App\Models\TelegramSetting;
use App\Services\Telegram\IncomingUpdate\Exceptions\FailedToReceiveIncomingUpdates;
use Database\Factories\PersonFactory;
use Mockery\MockInterface;
use NotificationChannels\Telegram\TelegramUpdates;
use Tests\TestCase;

class ProcessIncomingTelegramUpdatesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();
    }

    public function testConfirmationJoinChatbotMessageHandleSuccess(): void
    {
        $person = PersonFactory::new()->for($this->company)->withInvitationSent()->createOne();

        $chatId = fake()->randomNumber();

        $this->partialMock(TelegramUpdates::class, function (MockInterface $mock) use ($person, $chatId) {
            $mock->shouldReceive('get')
                ->twice()
                ->andReturn(
                    [
                        'ok' => true,
                        'result' => [
                            [
                                'update_id' => fake()->randomNumber(),
                                'message' => [
                                    'text' => '/start ' . $person->telegram_chat_invite_token,
                                    'chat' => ['id' => $chatId]
                                ]
                            ]
                        ]
                    ],
                    [
                        'ok' => true,
                        'result' => []
                    ]
                );
        });

        $this->artisan(ProcessIncomingTelegramUpdates::class)->assertSuccessful();

        $person->refresh();

        $this->assertEquals($chatId, $person->telegram_chat_id);
        $this->assertTrue($person->joinedToChatbot());
    }

    public function testPersonNotFoundByInviteTokenFail(): void
    {
        $person = PersonFactory::new()->for($this->company)->withInvitationSent()->createOne();

        $nonExistentInviteToken = fake()->uuid();
        $this->partialMock(TelegramUpdates::class, function (MockInterface $mock) use ($nonExistentInviteToken) {
            $mock->shouldReceive('get')
                ->twice()
                ->andReturn(
                    [
                        'ok' => true,
                        'result' => [
                            [
                                'update_id' => fake()->randomNumber(),
                                'message' => [
                                    'text' => '/start ' . $nonExistentInviteToken,
                                    'chat' => ['id' => fake()->randomNumber()]
                                ]
                            ]
                        ]
                    ],
                    [
                        'ok' => true,
                        'result' => []
                    ]
                );
        });

        $this->artisan(ProcessIncomingTelegramUpdates::class)->assertSuccessful();

        $person->refresh();

        $this->assertNull($person->telegram_chat_id);
        $this->assertFalse($person->joinedToChatbot());
    }

    public function testChatMemberBannedMessageHandleSuccess(): void
    {
        $person = PersonFactory::new()->for($this->company)->withJoinedToChatbot()->createOne();

        $this->partialMock(TelegramUpdates::class, function (MockInterface $mock) use ($person) {
            $mock->shouldReceive('get')
                ->twice()
                ->andReturn(
                    [
                        'ok' => true,
                        'result' => [
                            [
                                'update_id' => fake()->randomNumber(),
                                'my_chat_member' => [
                                    'chat' => ['id' => $person->telegram_chat_id],
                                    'new_chat_member' => ['status' => 'kicked']
                                ]
                            ]
                        ]
                    ],
                    [
                        'ok' => true,
                        'result' => []
                    ]
                );
        });

        $this->artisan(ProcessIncomingTelegramUpdates::class)->assertSuccessful();

        $person->refresh();

        $this->assertNull($person->telegram_chat_id);
        $this->assertFalse($person->joinedToChatbot());
    }

    public function testPersonNotFoundByChatIdFail(): void
    {
        $person = PersonFactory::new()->for($this->company)->withJoinedToChatbot()->createOne();

        $nonExistentChatId = fake()->numberBetween(100);
        $this->partialMock(TelegramUpdates::class, function (MockInterface $mock) use ($nonExistentChatId) {
            $mock->shouldReceive('get')
                ->twice()
                ->andReturn(
                    [
                        'ok' => true,
                        'result' => [
                            [
                                'update_id' => fake()->randomNumber(),
                                'my_chat_member' => [
                                    'chat' => ['id' => $nonExistentChatId],
                                    'new_chat_member' => ['status' => 'kicked']
                                ]
                            ]
                        ]
                    ],
                    [
                        'ok' => true,
                        'result' => []
                    ]
                );
        });

        $this->artisan(ProcessIncomingTelegramUpdates::class)->assertSuccessful();

        $person->refresh();

        $this->assertNotNull($person->telegram_chat_id);
        $this->assertTrue($person->joinedToChatbot());
    }

    public function testUnknownMessageHandleSuccess(): void
    {
        $updateId = fake()->randomNumber();

        $this->partialMock(TelegramUpdates::class, function (MockInterface $mock) use ($updateId) {
            $mock->shouldReceive('get')
                ->twice()
                ->andReturn(
                    [
                        'ok' => true,
                        'result' => [
                            [
                                'update_id' => $updateId
                            ]
                        ]
                    ],
                    [
                        'ok' => true,
                        'result' => []
                    ]
                );
        });

        $this->artisan(ProcessIncomingTelegramUpdates::class)->assertSuccessful();

        $this->assertDatabaseHas(TelegramSetting::class, ['bot_last_update_id' => $updateId]);
    }

    public function testFailedToReceiveIncomingUpdates(): void
    {
        $this->expectException(FailedToReceiveIncomingUpdates::class);

        $this->partialMock(TelegramUpdates::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->once()
                ->andReturn(['ok' => false]);
        });

        $this->artisan(ProcessIncomingTelegramUpdates::class)->assertSuccessful();
    }
}
