<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Recalc\ByInsured;

use App\UseCases\Command;

readonly class RecalcBalanceByInsuredCommand implements Command
{
    public function __construct(
        public int $insured_person_id
    ) {
    }
}
