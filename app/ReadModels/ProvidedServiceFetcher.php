<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\ProvidedService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProvidedServiceFetcher
{
    public function getByInsuredPerson(
        int $insuredPersonId,
        int $limit,
        int $page,
        array $filter
    ): LengthAwarePaginator {
        return $this->builder($filter)
            ->where('insured_person_id', '=', $insuredPersonId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getByContract(int $contractId, int $limit, int $page, array $filter): LengthAwarePaginator
    {
        return $this->builder($filter)
            ->where('contract_id', '=', $contractId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getOne(int $insuredPersonId, int $providedServiceId): Model|Builder|ProvidedService
    {
        return ProvidedService::query()
            ->where('insured_person_id', '=', $insuredPersonId)
            ->where('id', '=', $providedServiceId)
            ->firstOrFail();
    }

    public function getExpenseByService(int $insuredPersonId, int $serviceId): object
    {
        return ProvidedService::query()
            ->select(DB::raw('COALESCE(sum(quantity), 0) AS quantity, COALESCE(sum(amount), 0) AS amount'))
            ->where('insured_person_id', $insuredPersonId)
            ->where('service_id', $serviceId)
            ->first();
    }

    public function getAmountByInsuredPersonId(int $insuredPersonId): float
    {
        return (float)ProvidedService::query()
            ->where('insured_person_id', $insuredPersonId)
            ->sum('amount');
    }

    public function isServiceProvidedInContract(int $contractId, int $serviceId): bool
    {
        return ProvidedService::query()
            ->select('id')
            ->where('contract_id', '=', $contractId)
            ->where('service_id', '=', $serviceId)
            ->exists();
    }

    private function builder(array $filter): Builder
    {
        $builder = ProvidedService::query();

        if (isset($filter['service_id'])) {
            $builder->where('service_id', '=', $filter['service_id']);
        }

        if (isset($filter['service_name'])) {
            $builder->where('service_name', 'LIKE', '%' . $filter['service_name'] . '%');
        }

        if (isset($filter['date_of_service_from'])) {
            $builder->where('date_of_service', '>=', $filter['date_of_service_from']);
        }
        if (isset($filter['date_of_service_to'])) {
            $builder->where('date_of_service', '<=', $filter['date_of_service_to']);
        }

        if (isset($filter['limit_type'])) {
            $builder->where('limit_type', '=', $filter['limit_type']);
        }

        if (isset($filter['quantity_from'])) {
            $builder->where('quantity', '>=', $filter['quantity_from']);
        }
        if (isset($filter['quantity_to'])) {
            $builder->where('quantity', '<=', $filter['quantity_to']);
        }

        if (isset($filter['price_from'])) {
            $builder->where('price', '>=', $filter['price_from']);
        }
        if (isset($filter['price_to'])) {
            $builder->where('price', '<=', $filter['price_to']);
        }

        if (isset($filter['amount_from'])) {
            $builder->where('amount', '>=', $filter['amount_from']);
        }
        if (isset($filter['amount_to'])) {
            $builder->where('amount', '<=', $filter['amount_to']);
        }

        return $builder
            ->orderBy('date_of_service');
    }
}
