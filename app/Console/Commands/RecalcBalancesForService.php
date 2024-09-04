<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exceptions\Balance\RecalculationOfBalance;
use App\UseCases\Balance\Recalc\ByService\RecalcBalanceByServiceCommand;
use App\UseCases\Balance\Recalc\ByService\RecalcBalanceByServiceHandler;
use Illuminate\Console\Command;

class RecalcBalancesForService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalc-balances-for-service 
        {contractId : The ID of the contract} 
        {serviceId : The ID of the service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate balances for service';

    /**
     * Execute the console command.
     * @throws RecalculationOfBalance
     */
    public function handle(RecalcBalanceByServiceHandler $handler): void
    {
        $command = new RecalcBalanceByServiceCommand(
            (int)$this->argument('contractId'),
            (int)$this->argument('serviceId')
        );

        $handler->handle($command);
    }
}
