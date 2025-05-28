<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\ContractService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ContractServiceFetcher
{
    public function get(int $contractId, int $limit, int $page, array $filter): LengthAwarePaginator
    {
        return $this->builder($filter)
            ->where('contract_id', '=', $contractId)
            ->orderBy('service_id')
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getOne(int $contractId, int $serviceId): ContractService
    {
        return $this->builder()
            ->where('contract_id', '=', $contractId)
            ->where('service_id', '=', $serviceId)
            ->firstOrFail();
    }

    public function isExist(int $contractId, int $serviceId): bool
    {
        return $this->builder()
            ->select('id')
            ->where('contract_id', '=', $contractId)
            ->where('service_id', '=', $serviceId)
            ->exists();
    }

    private function builder(array $filter = []): Builder
    {
        $builder = ContractService::query();

        if (isset($filter['limit_type'])) {
            $builder->where('limit_type', '=', $filter['limit_type']);
        }

        if (isset($filter['limit_value_from'])) {
            $builder->where('limit_value', '>=', $filter['limit_value_from']);
        }
        if (isset($filter['limit_value_to'])) {
            $builder->where('limit_value', '<=', $filter['limit_value_to']);
        }

        return $builder;
    }
}
