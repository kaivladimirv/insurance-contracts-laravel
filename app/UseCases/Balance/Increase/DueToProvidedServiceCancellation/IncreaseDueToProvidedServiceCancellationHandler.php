<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Increase\DueToProvidedServiceCancellation;

use App\Models\Balance;
use App\Models\Builders\BalanceBuilder;
use App\Models\ProvidedService;
use App\Notifications\Balance\BalanceIncreasedDueToProvidedServiceCancellation;
use App\ReadModels\BalanceFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Illuminate\Database\Eloquent\Model;

class IncreaseDueToProvidedServiceCancellationHandler implements CommandHandler
{
    public function __construct(private readonly BalanceBuilder $balanceBuilder, private readonly BalanceFetcher $balanceFetcher)
    {
    }

    public function handle(IncreaseDueToProvidedServiceCancellationCommand|Command $command): void
    {
        /** @var ProvidedService $providedService */
        $providedService = ProvidedService::onlyTrashed()->findOrFail($command->providedServiceId);

        $balance = $this->getBalance($providedService) ?? $this->buildNewBalance($providedService);
        $balance->add($providedService->getValue());
        $balance->save();

        $balance->insuredPerson->person->notify(new BalanceIncreasedDueToProvidedServiceCancellation($balance, $providedService));
    }

    private function getBalance(ProvidedService $providedService): ?Balance
    {
        return $this->balanceFetcher->findOneByInsuredPersonAndService(
            $providedService->insured_person_id,
            $providedService->service_id
        );
    }

    private function buildNewBalance(ProvidedService $providedService): Model|Balance
    {
        return $this->balanceBuilder
            ->withContractId($providedService->contract_id)
            ->withServiceId($providedService->service_id)
            ->withInsuredPersonId($providedService->insured_person_id)
            ->build();
    }
}
