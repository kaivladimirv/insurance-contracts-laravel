<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Company\CompanyEmailChanged;
use App\Events\Company\CompanyPasswordChanged;
use App\Events\Company\CompanyRegistered;
use App\Models\Company;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class CompanyEventSubscriber implements ShouldQueue
{
    public function handleRegistered(CompanyRegistered $event): void
    {
        /** @var Company $company */
        $company = Company::query()->findOrFail($event->companyId);

        $message = (new \App\Mail\CompanyRegistered($company))->onQueue('emails');
        Mail::to($company->email)->queue($message);
    }

    public function handleEmailChanged(CompanyEmailChanged $event): void
    {
        /** @var Company $company */
        $company = Company::query()->findOrFail($event->companyId);

        $message = (new \App\Mail\CompanyEmailChanged($company))->onQueue('emails');
        Mail::to($event->newEmail)->queue($message);

        $company->tokens()->delete();
    }

    public function handlePasswordChanged(CompanyPasswordChanged $event): void
    {
        /** @var Company $company */
        $company = Company::query()->findOrFail($event->companyId);
        $company->tokens()->delete();
    }

    public function subscribe(): array
    {
        return [
            CompanyRegistered::class => 'handleRegistered',
            CompanyEmailChanged::class => 'handleEmailChanged',
            CompanyPasswordChanged::class => 'handlePasswordChanged'
        ];
    }
}
