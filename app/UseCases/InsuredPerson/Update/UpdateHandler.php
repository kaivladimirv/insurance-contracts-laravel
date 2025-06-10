<?php

declare(strict_types=1);

namespace App\UseCases\InsuredPerson\Update;

use App\ReadModels\InsuredPersonFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Override;

readonly class UpdateHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private InsuredPersonFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(Command $command): void
    {
        /** @var UpdateCommand $command */

        $insuredPerson = $this->fetcher->getOne($command->contract_id, $command->insured_person_id);

        $insuredPerson->fill($this->extractFillableData($command, $insuredPerson));
        $insuredPerson->save();
    }
}
