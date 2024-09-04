<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\Company\CompanyEmailChanged;
use App\Events\Company\CompanyPasswordChanged;
use App\Events\Company\CompanyRegistered;
use App\Listeners\CompanyEventSubscriber;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CompanyEventSubscriberTest extends TestCase
{
    private CompanyEventSubscriber $subscriber;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        Event::fake();
        Mail::fake();

        $this->subscriber = App::make(CompanyEventSubscriber::class);
    }

    public function testEmailIsSentAfterRegistered(): void
    {
        $event = new CompanyRegistered($this->company->id);
        $this->subscriber->handleRegistered($event);

        Mail::assertQueued(\App\Mail\CompanyRegistered::class);
    }

    public function testEmailIsSentAndTokensWereRemovedAfterEmailChanged(): void
    {
        $this->assertTrue($this->company->tokens()->exists());

        $event = new CompanyEmailChanged($this->company->id, fake()->unique()->freeEmail());
        $this->subscriber->handleEmailChanged($event);

        Mail::assertQueued(\App\Mail\CompanyEmailChanged::class);
        $this->assertFalse($this->company->tokens()->exists());
    }

    public function testTokensWereRemovedAfterPasswordChanged(): void
    {
        $this->assertTrue($this->company->tokens()->exists());

        $event = new CompanyPasswordChanged($this->company->id);
        $this->subscriber->handlePasswordChanged($event);

        $this->assertFalse($this->company->tokens()->exists());
    }
}
