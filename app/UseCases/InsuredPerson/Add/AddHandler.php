<?php

declare(strict_types=1);

namespace App\UseCases\InsuredPerson\Add;

use App\Events\InsuredPerson\InsuredPersonAdded;
use App\Models\InsuredPerson;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class AddHandler extends AbstractHandler
{
    #[Override]
    public function handle(AddCommand|Command $command): int
    {
        $insuredPerson = new InsuredPerson();
        $insuredPerson->fill($this->extractFillableData($command, $insuredPerson));
        $insuredPerson->contract()->associate($command->contract_id);
        $insuredPerson->save();

        InsuredPersonAdded::dispatch($insuredPerson->id);

        return $insuredPerson->id;
    }
}
