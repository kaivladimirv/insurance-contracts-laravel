<?php

declare(strict_types=1);

namespace App\Models\Builders;

use App\Models\Balance;
use App\Models\ContractService;
use App\Models\InsuredPerson;
use App\ReadModels\ContractServiceFetcher;
use Illuminate\Database\Eloquent\Model;

class BalanceBuilder
{
    private ?int $contractId = null;
    private ?int $serviceId = null;
    private ?int $insuredPersonId = null;
    private ?ContractService $contractService = null;

    public function withContractId(int $contractId): self
    {
        $clone = clone $this;
        $clone->contractId = $contractId;

        return $clone;
    }

    public function withServiceId(int $serviceId): self
    {
        $clone = clone $this;
        $clone->serviceId = $serviceId;

        return $clone;
    }

    public function withInsuredPersonId(int $insuredPersonId): self
    {
        $clone = clone $this;
        $clone->insuredPersonId = $insuredPersonId;

        return $clone;
    }

    public function build(): Model|Balance
    {
        if (!$this->contractService) {
            $this->contractId = $this->contractId ?? InsuredPerson::query()->findOrFail($this->insuredPersonId)->contract_id;
            $this->contractService = app(ContractServiceFetcher::class)->getOne($this->contractId, $this->serviceId);
        }

        $balance = new Balance();
        $balance->service_id = $this->contractService->service_id;
        $balance->limit_type = $this->contractService->limit_type;
        $balance->balance = $this->contractService->limit_value;
        $balance->contract()->associate($this->contractId);

        if ($this->insuredPersonId) {
            $balance = $balance->insuredPerson()->associate($this->insuredPersonId);
        }

        return $balance;
    }
}
