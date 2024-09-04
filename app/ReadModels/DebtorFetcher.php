<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\Balance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DebtorFetcher
{
    public function getAllByCompanyId(int $companyId, int $limit, int $page, array $params): LengthAwarePaginator
    {
        return $this->builder($params)
            ->where('contracts.company_id', '=', $companyId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getAllByContractId(
        int $contractId,
        int $limit,
        int $page,
        array $params
    ): LengthAwarePaginator {
        return $this->builder($params)
            ->where('contracts.id', '=', $contractId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    private function builder(array $params): Builder
    {
        $builder = Balance::query()
            ->select(
                [
                    'balances.contract_id',
                    'balances.insured_person_id',
                    'balances.service_id',
                    'balances.limit_type',
                    DB::raw('abs(balances.balance) as debt'),
                ]
            )
            ->join('contracts', 'contracts.id', '=', 'balances.contract_id')
            ->where('balances.balance', '<', 0)
            ->orderBy('balances.contract_id')
            ->orderBy('balances.insured_person_id')
            ->orderBy('balances.service_id')
            ->orderBy('balances.limit_type')
            ->orderBy('balances.balance', 'desc');

        if (isset($params['service_id'])) {
            $builder->where('service_id', '=', $params['service_id']);
        }
        if (isset($params['debt_from'])) {
            $builder->where(DB::raw('abs(balances.balance)'), '>=', (int)$params['debt_from']);
        }
        if (isset($params['debt_to'])) {
            $builder->where(DB::raw('abs(balances.balance)'), '<=', (int)$params['debt_to']);
        }

        return $builder;
    }
}
