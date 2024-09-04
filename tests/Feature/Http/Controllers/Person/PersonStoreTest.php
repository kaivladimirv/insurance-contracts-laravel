<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Person;

use App\Enums\NotifierType;
use App\Enums\TelegramChatStatus;
use App\Events\Person\PersonAdded;
use App\Models\Person;
use App\Notifications\Person\InvitationToJoinChatBot;
use Database\Factories\PersonFactory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Override;
use Tests\TestCase;

class PersonStoreTest extends TestCase
{
    private const string ROUTE_NAME = 'persons.store';

    private PersonFactory $personFactory;
    private array $formData;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAuthorizedByToken();

        $this->personFactory = Person::factory()->for($this->company);
        $this->formData = $this->personFactory->make()->toArray();
    }

    public function testSuccess(): void
    {
        Event::fake();

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(Person::class, $this->formData);
        Event::assertDispatched(PersonAdded::class);
    }

    public function testNotificationByEmailSuccess(): void
    {
        $this->formData['notifier_type'] = NotifierType::EMAIL;

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);
    }

    public function testNotificationByTelegramSuccess(): void
    {
        $this->formData['notifier_type'] = NotifierType::TELEGRAM;

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);
    }

    public function testSendInvitationToJoinChatBotSuccess(): void
    {
        Notification::fake();
        $this->formData['notifier_type'] = NotifierType::TELEGRAM;

        $response = $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        $person = Person::query()->findOrFail($response->json('id'));

        Notification::assertSentTo($person, InvitationToJoinChatBot::class);
    }

    public function testMarkThatInviteToJoinChatBotHasBeenSentSuccess(): void
    {
        $this->formData['notifier_type'] = NotifierType::TELEGRAM;

        $response = $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        /** @var Person $person */
        $person = Person::query()->findOrFail($response->json('id'));

        $this->assertNotNull($person->telegram_invite_date_for_chat);
        $this->assertEquals(TelegramChatStatus::INVITATION_SENT, $person->telegram_chat_status);
    }

    public function testNotificationsAreDisabledSuccess(): void
    {
        $this->formData['notifier_type'] = null;
        $this->formData['email'] = null;
        $this->formData['phone_number'] = null;

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(Person::class, $this->formData);
    }

    public function testLastNameRequiredFail(): void
    {
        $this->formData['last_name'] = '';

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['last_name' => 'The last name field is required']);
    }

    public function testFirstNameRequiredFail(): void
    {
        $this->formData['first_name'] = '';

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['first_name' => 'The first name field is required']);
    }

    public function testMiddleNameRequiredFail(): void
    {
        $this->formData['middle_name'] = '';

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['middle_name' => 'The middle name field is required']);
    }

    public function testEmailUniqueFail(): void
    {
        $this->formData['email'] = $this->personFactory->createOne()->email;

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => 'The email has already been taken']);
    }

    public function testPhoneNumberUniqueFail(): void
    {
        $this->formData['phone_number'] = $this->personFactory->createOne()->phone_number;

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone_number' => 'The phone number has already been taken']);
    }

    public function testEmailRequiredIfNotificationByEmailFail(): void
    {
        $this->formData['email'] = '';
        $this->formData['notifier_type'] = NotifierType::EMAIL;

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => 'The email field is required when notifier type is ' . NotifierType::EMAIL->value]);
    }

    public function testPhoneNumberRequiredIfNotificationByTelegramFail(): void
    {
        $this->formData['phone_number'] = '';
        $this->formData['notifier_type'] = NotifierType::TELEGRAM;

        $this->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone_number' => 'The phone number field is required when notifier type is ' . NotifierType::TELEGRAM->value]);
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson(route(self::ROUTE_NAME), $this->formData)
            ->assertUnauthorized();
    }
}
