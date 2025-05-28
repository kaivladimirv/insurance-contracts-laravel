<?php

declare(strict_types=1);

namespace App\UseCases\Person\Add;

use App\Events\Person\PersonAdded;
use App\Models\Person;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Override;

readonly class AddHandler implements CommandHandler
{
    #[Override]
    public function handle(AddCommand|Command $command): int
    {
        $person = new Person();
        $person->fill($command->only(...$person->getFillable())->toArray());
        $person->company()->associate($command->company_id);
        $person->save();

        PersonAdded::dispatch(
            $person->company_id,
            $person->id,
            $person->notifier_type
        );

        return $person->id;
    }
}
