<?php

declare(strict_types=1);

namespace App\UseCases\Company\ChangeEmail;

use App\Events\Company\CompanyEmailChanged;
use App\Models\Company;
use App\ReadModels\CompanyFetcher;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

readonly class ChangeEmailHandler implements CommandHandler
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
    public function handle(ChangeEmailCommand|Command $command): void
    {
        /** @var Company $company */
        $company = Company::query()->findOrFail($command->company_id);

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
