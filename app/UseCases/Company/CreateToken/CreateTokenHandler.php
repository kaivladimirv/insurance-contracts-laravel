<?php

declare(strict_types=1);

namespace App\UseCases\Company\CreateToken;

use App\Models\Company;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Laravel\Sanctum\NewAccessToken;

readonly class CreateTokenHandler implements CommandHandler
{
    public function handle(CreateTokenCommand|Command $command): NewAccessToken
    {
        /** @var Company $company */
        $company = Company::query()->findOrFail($command->companyId);

        $company->tokens()->delete();

        return $company->createAccessToken();
    }
}
