<?php

declare(strict_types=1);

namespace App\UseCases\Company\ConfirmEmailChange;

use App\Models\Company;
use App\ReadModels\CompanyFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;

readonly class ConfirmEmailChangeHandler implements CommandHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private CompanyFetcher $fetcher)
    {
    }

    public function handle(ConfirmEmailChangeCommand|Command $command): void
    {
        /** @var Company $company */
        $company = $this->fetcher->getOneByNewEmailConfirmToken($command->company_id, $command->new_email_confirm_token);

        $company->email = $company->new_email;
        $company->new_email = null;
        $company->new_email_confirm_token = null;
        $company->save();
    }
}
