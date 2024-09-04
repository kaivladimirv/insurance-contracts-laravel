<?php

declare(strict_types=1);

namespace Tests\Database\Commands;

use App\Console\Commands\RecalcBalancesForInsured;
use App\UseCases\Balance\Recalc\ByInsured\RecalcBalanceByInsuredHandler;
use Tests\TestCase;

class RecalcBalancesForInsuredCommandTest extends TestCase
{
    public function testSuccess(): void
    {
        $this->mock(RecalcBalanceByInsuredHandler::class)
            ->shouldReceive('handle')->once();

        $this->artisan(RecalcBalancesForInsured::class, [
            'insuredPersonId' => 1
        ])->assertSuccessful();
    }
}
