<?php

namespace Tests;

use App\Models\Company;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use LazilyRefreshDatabase;

    protected Company $company;

    protected function companyAuthorizedByToken(): void
    {
        $this->company = Company::factory()->createOne();

        $this->withToken($this->company->createAccessToken()->plainTextToken);
    }
}
