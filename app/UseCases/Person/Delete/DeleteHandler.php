<?php

declare(strict_types=1);

namespace App\UseCases\Person\Delete;

use App\Exceptions\InUse;
use App\ReadModels\PersonFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class DeleteHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private PersonFetcher $fetcher)
    {
    }

    /**
     * @throws InUse
     */
    public function handle(DeleteCommand|Command $command): void
    {
        $person = $this->fetcher->getOne($command->company_id, $command->id);

        if ($person->insuredPersons()->select('id')->exists()) {
            throw new InUse(__('The person is the insured person'));
        }

        $person->delete();
    }
}
