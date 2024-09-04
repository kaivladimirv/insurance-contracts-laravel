<?php

declare(strict_types=1);

namespace App\UseCases\InsuredPerson\Delete;

use App\Exceptions\InUse;
use App\ReadModels\InsuredPersonFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class DeleteHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private InsuredPersonFetcher $fetcher)
    {
    }

    /**
     * @throws InUse
     */
    public function handle(DeleteCommand|Command $command): void
    {
        $insuredPerson = $this->fetcher->getOne($command->contract_id, $command->insured_person_id);

        if ($insuredPerson->providedServices()->select('id')->exists()) {
            throw new InUse(__('Services have already been provided to the insured person'));
        }

        $insuredPerson->delete();
    }
}
