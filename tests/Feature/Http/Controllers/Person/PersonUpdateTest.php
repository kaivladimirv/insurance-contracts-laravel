<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Person;

use App\Enums\NotifierType;
use App\Enums\TelegramChatStatus;
use App\Events\Person\PersonUpdated;
use App\Models\Person;
use App\Notifications\Person\InvitationToJoinChatBot;
use Database\Factories\PersonFactory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Override;
use Tests\TestCase;

class PersonUpdateTest extends TestCase
{
    private const string ROUTE_NAME = 'persons.update';

    private PersonFactory $personFactory;
    private Person $person;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->personFactory = Person::factory()->for($this->company);
        $this->person = $this->personFactory->createOne();
    }

    public function testSuccess(): void
    {
        Event::fake();

        $formData = $this->personFactory->make()->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertNoContent();

        $this->assertDatabaseHas(Person::class, array_merge($formData, ['id' => $this->person->id]));
        Event::dispatch(PersonUpdated::class, ['personId' => $this->person->id, 'hasNotifierTypeChanged' => false]);
    }

    public function testNotificationByEmailSuccess(): void
    {
        Event::fake();

        $this->person->notifier_type = NotifierType::TELEGRAM;
        $formData = $this->personFactory->make(['notifier_type' => NotifierType::EMAIL])->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertNoContent();

        $this->assertDatabaseHas(Person::class, array_merge($formData, ['id' => $this->person->id]));
        Event::dispatch(PersonUpdated::class, ['personId' => $this->person->id, 'hasNotifierTypeChanged' => true]);
    }

    public function testNotificationByTelegramSuccess(): void
    {
        Event::fake();

        $this->person->notifier_type = NotifierType::EMAIL;
        $formData = $this->personFactory->make(['notifier_type' => NotifierType::TELEGRAM])->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertNoContent();

        $this->assertDatabaseHas(Person::class, array_merge($formData, ['id' => $this->person->id]));
        Event::dispatch(PersonUpdated::class, ['personId' => $this->person->id, 'hasNotifierTypeChanged' => true]);
    }

    public function testSendInvitationToJoinChatBotSuccess(): void
    {
        Notification::fake();

        $this->person->setAttribute('notifier_type', NotifierType::EMAIL)->save();
        $formData = $this->person->setAttribute('notifier_type', NotifierType::TELEGRAM)->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertNoContent();

        $this->person->refresh();

        Notification::assertSentTo($this->person, InvitationToJoinChatBot::class, function ($notification) {
            $mailText = $notification->toMail($this->person)->render()->toHtml();
            $this->assertStringContainsString(__('Join InsuranceContract ChatBot on Telegram'), $mailText);

            return true;
        });
    }

    public function testMarkThatInviteToJoinChatBotHasBeenSentSuccess(): void
    {
        $this->person->setAttribute('notifier_type', NotifierType::EMAIL)->save();
        $formData = $this->person->setAttribute('notifier_type', NotifierType::TELEGRAM)->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertNoContent();

        $this->person->refresh();

        $this->assertNotNull($this->person->telegram_invite_date_for_chat);
        $this->assertEquals(TelegramChatStatus::INVITATION_SENT, $this->person->telegram_chat_status);
    }

    public function testNotificationsAreDisabledSuccess(): void
    {
        $formData = $this->personFactory->make(['notifier_type' => null])->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertNoContent();

        $this->assertDatabaseHas(Person::class, array_merge($formData, ['id' => $this->person->id]));
    }

    public function testLastNameRequiredFail(): void
    {
        $formData = $this->person->makeHidden('last_name')->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['last_name' => 'The last name field is required']);
    }

    public function testFirstNameRequiredFail(): void
    {
        $formData = $this->person->makeHidden('first_name')->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['first_name' => 'The first name field is required']);
    }

    public function testMiddleNameRequiredFail(): void
    {
        $formData = $this->person->makeHidden('middle_name')->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['middle_name' => 'The middle name field is required']);
    }

    public function testEmailUniqueFail(): void
    {
        $existingEmail = $this->personFactory->createOne()->email;
        $formData = $this->person->setAttribute('email', $existingEmail)->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => 'The email has already been taken']);
    }

    public function testPhoneNumberUniqueFail(): void
    {
        $existingPhoneNumber = $this->personFactory->createOne()->phone_number;
        $formData = $this->person->setAttribute('phone_number', $existingPhoneNumber)->toArray();

        $this->postJson(route(self::ROUTE_NAME, $this->person), $formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone_number' => 'The phone number has already been taken']);
    }

    public function testNotFoundFail(): void
    {
        $nonExistentPersonId = -1;
        $formData = $this->personFactory->make()->toArray();

        $this->postJson(route(self::ROUTE_NAME, $nonExistentPersonId), $formData)
            ->assertNotFound();
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME, $this->person))
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME, $this->person))
            ->assertUnauthorized();
    }
}
