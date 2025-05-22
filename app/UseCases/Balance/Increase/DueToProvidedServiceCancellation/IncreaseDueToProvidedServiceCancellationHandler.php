<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Increase\DueToProvidedServiceCancellation;

use App\Dto\ProvidedServiceDto;
use App\Events\Balance\BalanceWasIncreasedDueToProvidedService;
use App\Models\Balance;
use App\Models\Builders\BalanceBuilder;
use App\ReadModels\BalanceFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Illuminate\Database\Eloquent\Model;
use Override;

class IncreaseDueToProvidedServiceCancellationHandler implements CommandHandler
{
    public function __construct(
        private readonly BalanceBuilder $balanceBuilder,
        private readonly BalanceFetcher $balanceFetcher
    ) {
    }

    #[Override]
    public function handle(Command $command): void
    {
        /** @var IncreaseDueToProvidedServiceCancellationCommand $command */

        $balance = $this->getBalance($command->providedServiceDto) ?? $this->buildNewBalance($command->providedServiceDto);
        $balance->add($command->providedServiceDto->value);
        $balance->save();

        BalanceWasIncreasedDueToProvidedService::dispatch($balance->balance, $command->providedServiceDto);
    }

    private function getBalance(ProvidedServiceDto $providedServiceDto): ?Balance
    {
        return $this->balanceFetcher->findOneByInsuredPersonAndService(
            $providedServiceDto->insuredPersonId,
            $providedServiceDto->serviceId
        );
    }

    private function buildNewBalance(ProvidedServiceDto $providedServiceDto): Model|Balance
    {
        return $this->balanceBuilder
            ->withContractId($providedServiceDto->contractId)
            ->withServiceId($providedServiceDto->serviceId)
            ->withInsuredPersonId($providedServiceDto->insuredPersonId)
            ->build();
    }
}
