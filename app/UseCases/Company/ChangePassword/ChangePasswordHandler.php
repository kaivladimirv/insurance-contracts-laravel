<?php

declare(strict_types=1);

namespace App\UseCases\Company\ChangePassword;

use App\Events\Company\CompanyPasswordChanged;
use App\Models\Company;
use App\ReadModels\CompanyFetcher;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Override;

readonly class ChangePasswordHandler extends AbstractHandler
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
        /** @var ChangePasswordCommand $command */

        $company = $this->fetcher->getOne($command->company_id);

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
