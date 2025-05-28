<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Services\CurrentCompanyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Override;
use RuntimeException;

class CompanyScope implements Scope
{
    #[Override]
    public function apply(Builder $builder, Model $model): void
    {
        $currentCompanyService = app(CurrentCompanyService::class);

        if ($currentCompanyService->hasCompany()) {
            $builder->where($model->getTable() . '.company_id', $currentCompanyService->getCompanyId());
        } else {
            throw new RuntimeException("Cannot query '{$model->getTable()}' without a defined company context.");
        }
    }
}
