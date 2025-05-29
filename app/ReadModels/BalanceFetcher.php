<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\Balance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BalanceFetcher
{
    public function findOneByInsuredPersonAndService(int $insuredPersonId, int $serviceId): null|Balance|Model
    {
        return $this->builder()
            ->forInsuredPersonAndService($insuredPersonId, $serviceId)
            ->first();
    }

    /**
     * @return Collection<int, Balance>
     */
    public function getByInsuredPerson(int $insuredPersonId): Collection
    {
        return $this->builder()
            ->forInsuredPerson($insuredPersonId)
            ->orderBy('service_id')
            ->get();
    }

    private function builder(): Builder|Balance
    {
        return Balance::query();
    }
}
