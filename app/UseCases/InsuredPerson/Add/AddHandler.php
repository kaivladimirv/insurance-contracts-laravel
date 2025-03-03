<?php

declare(strict_types=1);

namespace App\UseCases\InsuredPerson\Add;

use App\Events\InsuredPerson\InsuredPersonAdded;
use App\Models\InsuredPerson;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Override;

readonly class AddHandler implements CommandHandler
{
    #[Override]
    public function handle(AddCommand|Command $command): int
    {
        $insuredPerson = new InsuredPerson();
        $insuredPerson->fill($command->only(...$insuredPerson->getFillable())->all());
        $insuredPerson->contract()->associate($command->contract_id);
        $insuredPerson->save();

        InsuredPersonAdded::dispatch($insuredPerson->id);

        return $insuredPerson->id;
    }
}
