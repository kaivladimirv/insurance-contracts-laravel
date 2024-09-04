<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Recalc\ByServiceAndInsuredPerson;

use App\UseCases\Command;

readonly class RecalcBalanceByServiceAndInsuredPersonCommand implements Command
{
    public function __construct(
        public int $contract_id,
        public int $service_id,
        public int $insured_person_id
    ) {
    }
}
