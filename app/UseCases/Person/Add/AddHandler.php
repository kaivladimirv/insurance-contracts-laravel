<?php

declare(strict_types=1);

namespace App\UseCases\Person\Add;

use App\Events\Person\PersonAdded;
use App\Models\Person;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class AddHandler extends AbstractHandler
{
    #[Override]
    public function handle(Command $command): int
    {
        /** @var AddCommand $command */

        $person = new Person();
        $person->fill($this->extractFillableData($command, $person));
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
