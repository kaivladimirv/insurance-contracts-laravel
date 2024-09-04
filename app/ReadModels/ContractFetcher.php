<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\Contract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContractFetcher
{
    public function get(int $companyId, int $limit, int $page, array $filter): LengthAwarePaginator
    {
        return $this->builder($filter)
            ->where('company_id', '=', $companyId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getOne(int $companyId, int $contractId): Model|Builder|Contract
    {
        return Contract::query()
            ->where('company_id', '=', $companyId)
            ->where('id', '=', $contractId)
            ->firstOrFail();
    }

    private function builder(array $filter): Builder
    {
        $builder = Contract::query();

        if (isset($filter['number'])) {
            $builder->where('number', 'LIKE', $filter['number'] . '%');
        }

        if (isset($filter['period_from']) and isset($filter['period_to'])) {
            $builder->where(function (Builder $query) use ($filter) {
                $query->whereBetween('start_date', [$filter['period_from'], $filter['period_to']])
                    ->orWhereBetween('end_date', [$filter['period_from'], $filter['period_to']])
                    ->orWhere('end_date', '>', $filter['period_to']);
            });
        } elseif (isset($filter['period_from'])) {
            $builder->where(function (Builder $query) use ($filter) {
                $query->where('start_date', '>=', $filter['period_from'])
                    ->orWhere('end_date', '>=', $filter['period_from']);
            });
        }

        if (isset($filter['max_amount_from'])) {
            $builder->where('max_amount', '>=', $filter['max_amount_from']);
        }
        if (isset($filter['max_amount_to'])) {
            $builder->where('max_amount', '<=', $filter['max_amount_to']);
        }

        return $builder
            ->orderBy('start_date')
            ->orderBy('number');
    }
}
