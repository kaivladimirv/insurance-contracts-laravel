<?php

declare(strict_types=1);

namespace App\UseCases\Person\Update;

use App\Events\Person\PersonUpdated;
use App\Models\Person;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class UpdateHandler implements CommandHandler
{
    public function handle(UpdateCommand|Command $command): void
    {
        /** @var Person $person */
        $person = Person::query()->findOrFail($command->id);
        $person->fill($command->only(...$person->getFillable())->toArray());

        if ($person->isDirty('phone_number')) {
            $person->telegram_chat_status = null;
            $person->telegram_chat_id = null;
        }

        $person->save();

        PersonUpdated::dispatch(
            $person->id,
            $person->wasChanged('notifier_type'),
            $person->wasChanged('phone_number')
        );
    }
}
