<?php

namespace Tests;

use App\Models\Company;
use App\Services\CurrentCompanyService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\App;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use LazilyRefreshDatabase;

    protected Company $company;

    protected function companyAuthorizedByToken(): void
    {
        $this->company = Company::factory()->createOne();

        $this->withToken($this->company->createAccessToken()->plainTextToken);

        App::make(CurrentCompanyService::class)->setCompanyId($this->company->id);
    }
}
