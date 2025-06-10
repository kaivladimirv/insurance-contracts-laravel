<?php

declare(strict_types=1);

namespace App\UseCases\Company\ChangeEmail;

use App\Events\Company\CompanyEmailChanged;
use App\ReadModels\CompanyFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Override;

readonly class ChangeEmailHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private CompanyFetcher $fetcher)
    {
    }

    /**
     * @throws ValidationException
     */
    #[Override]
    public function handle(Command $command): void
    {
        /** @var ChangeEmailCommand $command */

        $company = $this->fetcher->getOne($command->company_id);

        $this->assertEmailDoesNotMatched($company->email, $command->email);
        $this->assertEmailIsUnique($command);

        $company->new_email = $command->email;
        $company->new_email_confirm_token = Str::uuid()->toString();
        $company->save();

        CompanyEmailChanged::dispatch($company->id, $company->new_email);
    }

    /**
     * @throws ValidationException
     */
    private function assertEmailDoesNotMatched(string $oldEmail, string $newEmail): void
    {
        if ($oldEmail === $newEmail) {
            throw ValidationException::withMessages(['email' => __('New and old emails must not match')]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function assertEmailIsUnique(ChangeEmailCommand $command): void
    {
        if ($this->fetcher->existsByEmail($command->email, $command->company_id)) {
            throw ValidationException::withMessages(['email' => __('Email already in use')]);
        }
    }
}
