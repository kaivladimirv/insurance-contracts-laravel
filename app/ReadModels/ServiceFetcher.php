<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServiceFetcher
{
    public function get(int $companyId, int $limit, int $page, array $filter): LengthAwarePaginator
    {
        return $this->builder($filter)
            ->where('company_id', '=', $companyId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getOne(int $companyId, int $serviceId): Model|Builder|Service
    {
        return Service::query()
            ->where('company_id', '=', $companyId)
            ->where('id', '=', $serviceId)
            ->firstOrFail();
    }

    private function builder(array $filter): Builder
    {
        $builder = Service::query();

        if (isset($filter['name'])) {
            $builder->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        return $builder
            ->orderBy('name');
    }
}
