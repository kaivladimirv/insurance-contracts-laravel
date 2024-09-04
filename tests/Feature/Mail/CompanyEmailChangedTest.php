<?php

declare(strict_types=1);

namespace Tests\Feature\Mail;

use App\Mail\CompanyEmailChanged;
use Tests\TestCase;

class CompanyEmailChangedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();
    }

    public function testSuccess(): void
    {
        $mailable = new CompanyEmailChanged($this->company);
        $mailable->assertSeeInHtml($this->company->name);
        $mailable->assertSeeInHtml($this->company->new_email_confirm_token);
    }
}
