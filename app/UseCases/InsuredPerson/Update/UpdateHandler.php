<?php

declare(strict_types=1);

namespace App\UseCases\InsuredPerson\Update;

use App\ReadModels\InsuredPersonFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class UpdateHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private InsuredPersonFetcher $fetcher)
    {
    }

    public function handle(UpdateCommand|Command $command): void
    {
        $insuredPerson = $this->fetcher->getOne($command->contract_id, $command->insured_person_id);

        $insuredPerson->fill($command->only(...$insuredPerson->getFillable())->all());
        $insuredPerson->save();
    }
}
