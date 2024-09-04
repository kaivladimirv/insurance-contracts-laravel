<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\Balance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BalanceFetcher
{
    public function findOneByInsuredPersonAndService(int $insuredPersonId, int $serviceId): null|Balance|Model
    {
        return Balance::query()
            ->where('insured_person_id', '=', $insuredPersonId)
            ->where('service_id', '=', $serviceId)
            ->first();
    }

    /**
     * @return Collection<int, Balance>
     */
    public function getByInsuredPerson(int $insuredPersonId): Collection
    {
        return Balance::query()
            ->where('insured_person_id', '=', $insuredPersonId)
            ->orderBy('service_id')
            ->get();
    }
}
