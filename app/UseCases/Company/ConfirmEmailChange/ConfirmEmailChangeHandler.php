<?php

declare(strict_types=1);

namespace App\UseCases\Company\ConfirmEmailChange;

use App\ReadModels\CompanyFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class ConfirmEmailChangeHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private CompanyFetcher $fetcher)
    {
    }

    #[Override]
    public function handle(Command $command): void
    {
        /** @var ConfirmEmailChangeCommand $command */

        $company = $this->fetcher->getOneByNewEmailConfirmToken($command->company_id, $command->new_email_confirm_token);

        $company->email = $company->new_email;
        $company->new_email = null;
        $company->new_email_confirm_token = null;
        $company->save();
    }
}
