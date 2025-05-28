<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ServiceFetcher
{
    public function get(int $limit, int $page, array $filter): LengthAwarePaginator
    {
        return $this->builder($filter)
            ->orderBy('name')
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getOne(int $serviceId): Service
    {
        return $this->builder()
            ->where('id', '=', $serviceId)
            ->firstOrFail();
    }

    private function builder(array $filter = []): Builder
    {
        $builder = Service::query();

        if (isset($filter['name'])) {
            $builder->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        return $builder;
    }
}
