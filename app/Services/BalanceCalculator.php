<?php

declare(strict_types=1);

namespace App\Services;

use App\ReadModels\ContractServiceFetcher;
use App\ReadModels\ProvidedServiceFetcher;

readonly class BalanceCalculator
{
    /**
     * @psalm-api
     */
    public function __construct(private ContractServiceFetcher $contractServiceFetcher, private ProvidedServiceFetcher $providedServiceFetcher)
    {
    }

    public function calcByServiceAndInsuredPerson(int $contractId, int $serviceId, int $insuredPersonId): float|int
    {
        $contractService = $this->contractServiceFetcher->getOne($contractId, $serviceId);
        $expense = $this->providedServiceFetcher->getExpenseByService($insuredPersonId, $serviceId);

        $expenseValue = ($contractService->limit_type->isItAmountLimiter() ? $expense->amount : $expense->quantity);

        return $contractService->limit_value - $expenseValue;
    }
}
