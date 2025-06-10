<?php

declare(strict_types=1);

namespace App\UseCases\Person\Update;

use App\Events\Person\PersonUpdated;
use App\ReadModels\PersonFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class UpdateHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private PersonFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(UpdateCommand|Command $command): void
    {
        $person = $this->fetcher->getOne($command->id);

        $person->fill($this->extractFillableData($command, $person));

        if ($person->isDirty('phone_number')) {
            $person->telegram_chat_status = null;
            $person->telegram_chat_id = null;
        }

        $person->save();

        PersonUpdated::dispatch(
            $person->company_id,
            $person->id,
            $person->notifier_type,
            $person->wasChanged('notifier_type'),
            $person->wasChanged('phone_number')
        );
    }
}
