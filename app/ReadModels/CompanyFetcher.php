<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CompanyFetcher
{
    public function getOne(int $companyId): Company
    {
        return $this->builder()->findOrFail($companyId);
    }

    public function getOneByEmailConfirmToken(string $emailConfirmToken): Company|Model
    {
        return $this->builder()->where('email_confirm_token', $emailConfirmToken)->firstOrFail();
    }

    public function getOneByNewEmailConfirmToken(int $companyId, string $token): Company|Model
    {
        return $this->builder()
            ->where('new_email_confirm_token', $token)
            ->findOrFail($companyId);
    }

    public function existsByEmail(string $email, ?int $excludeCompanyId): bool
    {
        $builder = $this->builder()->select('id')->where('email', $email);

        if ($excludeCompanyId !== null) {
            $builder->where('id', '<>', $excludeCompanyId);
        }

        return $builder->exists();
    }

    public function existsByName(string $name, ?int $excludeCompanyId): bool
    {
        $builder = $this->builder()->select('id')->where('name', $name);

        if ($excludeCompanyId !== null) {
            $builder->where('id', '<>', $excludeCompanyId);
        }

        return $builder->exists();
    }

    private function builder(): Builder
    {
        return Company::query();
    }
}
