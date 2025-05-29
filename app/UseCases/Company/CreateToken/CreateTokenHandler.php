<?php

declare(strict_types=1);

namespace App\UseCases\Company\CreateToken;

use App\ReadModels\CompanyFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Laravel\Sanctum\NewAccessToken;
use Override;

readonly class CreateTokenHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private CompanyFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(CreateTokenCommand|Command $command): NewAccessToken
    {
        $company = $this->fetcher->getOne($command->companyId);

        $company->tokens()->delete();

        return $company->createAccessToken();
    }
}
