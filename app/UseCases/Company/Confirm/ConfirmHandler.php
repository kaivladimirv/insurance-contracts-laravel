<?php

declare(strict_types=1);

namespace App\UseCases\Company\Confirm;

use App\ReadModels\CompanyFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class ConfirmHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private CompanyFetcher $fetcher)
    {
    }

    public function handle(ConfirmCommand|Command $command): void
    {
        $company = $this->fetcher->getOneByEmailConfirmToken($command->emailConfirmToken);
        $company->is_email_confirmed = true;
        $company->email_confirm_token = null;
        $company->save();
    }
}
