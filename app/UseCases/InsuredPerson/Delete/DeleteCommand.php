<?php

declare(strict_types=1);

namespace App\UseCases\InsuredPerson\Delete;

use App\UseCases\Command;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;

class DeleteCommand extends Data implements Command
{
    public function __construct(
        #[FromRouteParameter('contract_id')]
        readonly public int $contract_id,
        #[FromRouteParameter('insured_person_id')]
        readonly public int $insured_person_id,
    ) {
    }
}
