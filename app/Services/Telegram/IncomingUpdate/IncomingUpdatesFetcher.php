<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate;

use App\Services\Telegram\IncomingUpdate\Exceptions\FailedToReceiveIncomingUpdates;
use Generator;
use Illuminate\Support\Facades\App;
use NotificationChannels\Telegram\TelegramUpdates;

class IncomingUpdatesFetcher
{
    /**
     * @return Generator<array>
     * @throws FailedToReceiveIncomingUpdates
     */
    public function fetch(int $limit, int $offset): Generator
    {
        while ($incomingUpdates = $this->getUpdates($limit, $offset)) {
            foreach ($incomingUpdates as $incomingUpdate) {
                $offset = $incomingUpdate['update_id'] + 1;
                yield $incomingUpdate;
            }
        }
    }

    /**
     * @throws FailedToReceiveIncomingUpdates
     */
    private function getUpdates(int $limit, int $offset): array
    {
        $updates = App::make(TelegramUpdates::class)
            ->limit($limit)
            ->options(
                [
                    'offset' => $offset,
                    'allowed_updates' => ['message', 'my_chat_member']
                ]
            )
            ->get();

        if (!$updates['ok']) {
            throw new FailedToReceiveIncomingUpdates(__('Failed to receive incoming messages from chatbot'));
        }

        return $updates['result'];
    }
}
