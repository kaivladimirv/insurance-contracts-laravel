<?php

declare(strict_types=1);

namespace App\UseCases\Company\CreateToken;

use App\ReadModels\CompanyFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Laravel\Sanctum\NewAccessToken;
use Override;

readonly class CreateTokenHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private CompanyFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(Command $command): NewAccessToken
    {
        /** @var CreateTokenCommand $command */

        $company = $this->fetcher->getOne($command->companyId);

        $company->tokens()->delete();

        return $company->createAccessToken();
    }
}
