<?php

declare(strict_types=1);

namespace App\UseCases\Company\ChangePassword;

use App\Events\Company\CompanyPasswordChanged;
use App\Models\Company;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

readonly class ChangePasswordHandler implements CommandHandler
{
    /**
     * @throws ValidationException
     */
    public function handle(ChangePasswordCommand|Command $command): void
    {
        /** @var Company $company */
        $company = Company::query()->findOrFail($command->company_id);

        $this->assertPasswordDoesNotMatched($company, $command->password);

        $company->password_hash = Hash::make($command->password);
        $company->save();

        CompanyPasswordChanged::dispatch($company->id);
    }

    /**
     * @throws ValidationException
     */
    private function assertPasswordDoesNotMatched(Company $company, string $password): void
    {
        if ($company->isPasswordMatch($password)) {
            throw ValidationException::withMessages(['password' => __('New and old passwords must not match')]);
        }
    }
}
