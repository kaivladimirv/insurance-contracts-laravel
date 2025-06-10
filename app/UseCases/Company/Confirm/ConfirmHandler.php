<?php

declare(strict_types=1);

namespace App\UseCases\Company\Confirm;

use App\ReadModels\CompanyFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Override;

readonly class ConfirmHandler extends AbstractHandler
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
        /** @var ConfirmCommand $command */

        $company = $this->fetcher->getOneByEmailConfirmToken($command->emailConfirmToken);
        $company->is_email_confirmed = true;
        $company->email_confirm_token = null;
        $company->save();
    }
}
