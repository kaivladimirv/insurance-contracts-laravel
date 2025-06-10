<?php

declare(strict_types=1);

namespace App\UseCases\Company\Register;

use App\Events\Company\CompanyRegistered;
use App\Models\Company;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Override;

readonly class RegisterHandler extends AbstractHandler
{
    #[Override]
    public function handle(Command $command): void
    {
        /** @var RegisterCommand $command */

        $company = new Company();
        $company->fill($this->extractFillableData($command, $company));
        $company->email = $command->email;
        $company->password_hash = Hash::make($command->password);
        $company->email_confirm_token = Str::uuid()->toString();
        $company->save();

        CompanyRegistered::dispatch($company->id);
    }
}
