<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Company\CompanyEmailChanged;
use App\Events\Company\CompanyPasswordChanged;
use App\Events\Company\CompanyRegistered;
use App\Models\Company;
use App\ReadModels\CompanyFetcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class CompanyEventSubscriber implements ShouldQueue
{
    public function handleRegistered(CompanyRegistered $event): void
    {
        $company = $this->getCompany($event->companyId);

        $message = new \App\Mail\CompanyRegistered($company)->onQueue('emails');
        Mail::to($company->email)->queue($message);
    }

    public function handleEmailChanged(CompanyEmailChanged $event): void
    {
        $company = $this->getCompany($event->companyId);

        $message = new \App\Mail\CompanyEmailChanged($company)->onQueue('emails');
        Mail::to($event->newEmail)->queue($message);

        $company->tokens()->delete();
    }

    public function handlePasswordChanged(CompanyPasswordChanged $event): void
    {
        $company = $this->getCompany($event->companyId);
        $company->tokens()->delete();
    }

    private function getCompany(int $companyId): Company
    {
        return App::make(CompanyFetcher::class)->getOne($companyId);
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
