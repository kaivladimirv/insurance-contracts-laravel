<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class CompanyFetcher
{
    public function getOneByEmailConfirmToken(string $emailConfirmToken): Company|Model
    {
        return Company::query()->where('email_confirm_token', $emailConfirmToken)->firstOrFail();
    }

    public function getOneByNewEmailConfirmToken(int $companyId, string $token): Company|Model
    {
        return Company::query()
            ->where('id', $companyId)
            ->where('new_email_confirm_token', $token)
            ->firstOrFail();
    }

    public function existsByEmail(string $email, ?int $excludeCompanyId): bool
    {
        $builder = Company::query()->select('id')->where('email', $email);

        if ($excludeCompanyId !== null) {
            $builder->where('id', '<>', $excludeCompanyId);
        }

        return $builder->exists();
    }

    public function existsByName(string $name, ?int $excludeCompanyId): bool
    {
        $builder = Company::query()->select('id')->where('name', $name);

        if ($excludeCompanyId !== null) {
            $builder->where('id', '<>', $excludeCompanyId);
        }

        return $builder->exists();
    }
}
