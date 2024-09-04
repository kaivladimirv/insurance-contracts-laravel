<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Recalc\ByServiceAndInsuredPerson;

use App\Models\Balance;
use App\Models\Builders\BalanceBuilder;
use App\ReadModels\BalanceFetcher;
use App\Services\BalanceCalculator;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Illuminate\Database\Eloquent\Model;

readonly class RecalcBalanceByServiceAndInsuredPersonHandler implements CommandHandler
{
    public function __construct(private BalanceCalculator $balanceCalculator, private BalanceBuilder $balanceBuilder, private BalanceFetcher $balanceFetcher)
    {
    }

    public function handle(RecalcBalanceByServiceAndInsuredPersonCommand|Command $command): void
    {
        $calculatedBalanceValue = $this->balanceCalculator->calcByServiceAndInsuredPerson(
            $command->contract_id,
            $command->service_id,
            $command->insured_person_id
        );

        $balance = $this->getBalance($command) ?? $this->buildNewBalance($command);
        $balance->balance = $calculatedBalanceValue;
        $balance->save();
    }

    private function getBalance(RecalcBalanceByServiceAndInsuredPersonCommand $command): ?Balance
    {
        return $this->balanceFetcher->findOneByInsuredPersonAndService($command->insured_person_id, $command->service_id);
    }

    private function buildNewBalance(RecalcBalanceByServiceAndInsuredPersonCommand $command): Model|Balance
    {
        return $this->balanceBuilder
            ->withContractId($command->contract_id)
            ->withServiceId($command->service_id)
            ->withInsuredPersonId($command->insured_person_id)
            ->build();
    }
}
