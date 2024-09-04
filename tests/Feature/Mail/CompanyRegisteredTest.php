<?php

declare(strict_types=1);

namespace Tests\Feature\Mail;

use App\Mail\CompanyRegistered;
use Tests\TestCase;

class CompanyRegisteredTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();
    }

    public function testSuccess(): void
    {
        $mailable = new CompanyRegistered($this->company);
        $mailable->assertSeeInHtml($this->company->name);
        $mailable->assertSeeInHtml($this->company->email_confirm_token);
    }
}
