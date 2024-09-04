<?php

declare(strict_types=1);

namespace App\UseCases\Company\Register;

use App\Events\Company\CompanyRegistered;
use App\Models\Company;
use App\UseCases\Command;
use App\UseCases\CommandHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

readonly class RegisterHandler implements CommandHandler
{
    public function handle(RegisterCommand|Command $command): void
    {
        $company = new Company();
        $company->fill($command->only(...$company->getFillable())->all());
        $company->email = $command->email;
        $company->password_hash = Hash::make($command->password);
        $company->email_confirm_token = Str::uuid()->toString();
        $company->save();

        CompanyRegistered::dispatch($company->id);
    }
}
