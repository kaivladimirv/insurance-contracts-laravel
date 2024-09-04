<?php

declare(strict_types=1);

namespace App\Specifications;

use App\Exceptions\ProvidedService\ServiceAmountOrQuantityDoesExceedLimit;
use App\Models\ProvidedService;
use App\ReadModels\BalanceFetcher;
use App\ReadModels\ContractServiceFetcher;
use Kaivladimirv\LaravelSpecificationPattern\AbstractSpecification;
use Override;

class ServiceDoesNotExceedLimitSpecification extends AbstractSpecification
{
    /**
     * @psalm-api
     */
    public function __construct(private readonly ContractServiceFetcher $contractServiceFetcher, private readonly BalanceFetcher $balanceFetcher)
    {
    }

    #[Override]
    protected function defineMessage(): string
    {
        return '';
    }

    #[Override]
    protected function executeCheckIsSatisfiedBy(mixed $candidate): bool
    {
        /** @var ProvidedService $candidate */
        $insuredPerson = $candidate->insuredPerson;

        if ($insuredPerson->is_allowed_to_exceed_limit) {
            return true;
        }

        $contractService = $this->contractServiceFetcher->getOne($candidate->contract_id, $candidate->service_id);

        $balanceValue = $this->getBalance($insuredPerson->id, $candidate->service_id);
        $limitExceeded = $balanceValue < $candidate->getValue();

        if (!$limitExceeded) {
            return true;
        }

        $messageKey = $this->getTextMessage($candidate->limit_type->isItAmountLimiter());
        $this->setMessage(__($messageKey, ['limitValue' => $contractService->limit_value, 'balance' => $balanceValue]));

        return false;
    }

    private function getTextMessage(bool $isItAmountLimiter): string
    {
        return $isItAmountLimiter ? 'Service amount does exceed limit' : 'The number of service exceeds limit';
    }

    private function getBalance(int $insuredPersonId, int $serviceId): float
    {
        $balance = $this->balanceFetcher->findOneByInsuredPersonAndService($insuredPersonId, $serviceId);

        return !is_null($balance) ? $balance->balance : 0;
    }

    #[Override]
    protected function getExceptionClass(): string
    {
        return ServiceAmountOrQuantityDoesExceedLimit::class;
    }
}
