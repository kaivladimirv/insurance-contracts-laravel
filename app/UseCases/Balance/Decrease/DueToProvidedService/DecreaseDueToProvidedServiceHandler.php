<?php

declare(strict_types=1);

namespace App\UseCases\Balance\Decrease\DueToProvidedService;

use App\Dto\ProvidedServiceDto;
use App\Events\Balance\BalanceWasDecreasedDueToProvidedService;
use App\Models\Balance;
use App\Models\Builders\BalanceBuilder;
use App\ReadModels\BalanceFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Illuminate\Database\Eloquent\Model;
use Override;

class DecreaseDueToProvidedServiceHandler implements CommandHandler
{
    public function __construct(
        readonly private BalanceBuilder $balanceBuilder,
        readonly private BalanceFetcher $balanceFetcher
    ) {
    }

    #[Override]
    public function handle(Command $command): void
    {
        /** @var DecreaseDueToProvidedServiceCommand $command */

        $balance = $this->getBalance($command->providedServiceDto) ?? $this->buildNewBalance($command->providedServiceDto);
        $balance->subtract($command->providedServiceDto->value);
        $balance->save();

        BalanceWasDecreasedDueToProvidedService::dispatch($balance->balance, $command->providedServiceDto);
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
