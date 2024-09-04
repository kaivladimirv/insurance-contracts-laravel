<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\InsuredPerson;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InsuredPersonFetcher
{
    public function get(int $contractId, int $limit, int $page, array $filter): LengthAwarePaginator
    {
        return $this->builder($filter)
            ->where('contract_id', '=', $contractId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getAllIdsByContract(int $contractId): Collection
    {
        return InsuredPerson::query()
            ->where('contract_id', $contractId)
            ->pluck('id');
    }

    public function getOne(int $contractId, int $insuredPersonId): Model|Builder|InsuredPerson
    {
        return InsuredPerson::query()
            ->where('contract_id', '=', $contractId)
            ->where('id', '=', $insuredPersonId)
            ->firstOrFail();
    }

    private function builder(array $filter): Builder
    {
        $builder = InsuredPerson::query();

        if (isset($filter['policy_number'])) {
            $builder->where('policy_number', 'LIKE', $filter['policy_number'] . '%');
        }

        if (isset($filter['is_allowed_to_exceed_limit'])) {
            $builder->where('is_allowed_to_exceed_limit', '=', $filter['is_allowed_to_exceed_limit']);
        }

        return $builder
            ->orderBy('policy_number');
    }
}
