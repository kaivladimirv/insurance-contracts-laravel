<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Enums\NotifierType;
use App\Events\Person\PersonAdded;
use App\Events\Person\PersonUpdated;
use App\Listeners\PersonEventSubscriber;
use App\Models\Person;
use App\Notifications\Person\InvitationToJoinChatBot;
use Database\Factories\PersonFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PersonEventSubscriberTest extends TestCase
{
    private PersonEventSubscriber $subscriber;
    private Person $person;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->subscriber = App::make(PersonEventSubscriber::class);
        $this->person = PersonFactory::new()->createOne();
    }

    public function testSendInvitationToJoinChatBotSuccess(): void
    {
        Notification::fake();

        $this->person->notifier_type = NotifierType::TELEGRAM;
        $this->person->save();

        $event = new PersonUpdated($this->person->id, hasNotifierTypeChanged: false, hasPhoneNumberChanged: true);
        $this->subscriber->handlePersonUpdated($event);
        $this->person->refresh();

        Notification::assertSentTo($this->person, InvitationToJoinChatBot::class, function (InvitationToJoinChatBot $notification) {
            $mailText = $notification->toMail($this->person)->render()->toHtml();
            $this->assertStringContainsString(__('Join InsuranceContract ChatBot on Telegram'), $mailText);

            return true;
        });
    }

    public function testSendInvitationToJoinChatBotIfPhoneNumberChangedSuccess(): void
    {
        Notification::fake();

        $this->person->notifier_type = NotifierType::TELEGRAM;
        $this->person->save();

        $event = new PersonUpdated($this->person->id, hasNotifierTypeChanged: true, hasPhoneNumberChanged: false);
        $this->subscriber->handlePersonUpdated($event);
        $this->person->refresh();

        Notification::assertSentTo($this->person, InvitationToJoinChatBot::class, function (InvitationToJoinChatBot $notification) {
            $mailText = $notification->toMail($this->person)->render()->toHtml();
            $this->assertStringContainsString(__('Join InsuranceContract ChatBot on Telegram'), $mailText);

            return true;
        });
    }

    public function testInvitationToJoinChatBotWasNotSentIfNotifierTypeIsEmailSuccess(): void
    {
        Notification::fake();

        $this->person->notifier_type = NotifierType::EMAIL;
        $this->person->save();

        $event = new PersonUpdated($this->person->id, hasNotifierTypeChanged: true, hasPhoneNumberChanged: false);
        $this->subscriber->handlePersonUpdated($event);
        $this->person->refresh();

        Notification::assertNotSentTo($this->person, InvitationToJoinChatBot::class);
    }

    public function testInvitationToJoinChatBotWasNotSentIfNotifierTypeIsNotChangedSuccess(): void
    {
        Notification::fake();

        $this->person->notifier_type = NotifierType::TELEGRAM;
        $this->person->save();

        $event = new PersonUpdated($this->person->id, hasNotifierTypeChanged: false, hasPhoneNumberChanged: false);
        $this->subscriber->handlePersonUpdated($event);
        $this->person->refresh();

        Notification::assertNotSentTo($this->person, InvitationToJoinChatBot::class);
    }

    public function testInvitationHasAlreadyBeenSentFail(): void
    {
        $this->person->notifier_type = NotifierType::TELEGRAM;
        $this->person->telegram_chat_invite_token = fake()->uuid();
        $this->person->markThatInviteToJoinChatBotHasBeenSent(now());
        $this->person->save();

        Log::shouldReceive('error')->once()
            ->with(__('The invitation has already been sent'), ['person_id' => $this->person->id]);

        $event = new PersonAdded($this->person->id);
        $this->subscriber->handlePersonAdded($event);
    }
}
