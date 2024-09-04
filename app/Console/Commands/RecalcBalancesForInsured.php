<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exceptions\Balance\RecalculationOfBalance;
use App\UseCases\Balance\Recalc\ByInsured\RecalcBalanceByInsuredCommand;
use App\UseCases\Balance\Recalc\ByInsured\RecalcBalanceByInsuredHandler;
use Illuminate\Console\Command;

class RecalcBalancesForInsured extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalc-balances-for-insured 
        {insuredPersonId : The ID of the insured person}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate balances for insured person';

    /**
     * Execute the console command.
     * @throws RecalculationOfBalance
     */
    public function handle(RecalcBalanceByInsuredHandler $handler): void
    {
        $command = new RecalcBalanceByInsuredCommand(
            (int)$this->argument('insuredPersonId')
        );

        $handler->handle($command);
    }
}
