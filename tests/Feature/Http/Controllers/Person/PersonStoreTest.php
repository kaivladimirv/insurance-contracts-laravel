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
use PHPUnit\Framework\Attributes\DataProvider;
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
        $this->formData = $this->personFactory->makeOne()->toArray();
    }

    public function testSuccess(): void
    {
        Event::fake();

        $this->postJson($this->route(), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(Person::class, $this->formData);
        Event::assertDispatched(PersonAdded::class);
    }

    public function testNotificationByEmailSuccess(): void
    {
        $this->formData['notifier_type'] = NotifierType::EMAIL;

        $this->postJson($this->route(), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);
    }

    public function testNotificationByTelegramSuccess(): void
    {
        $this->formData['notifier_type'] = NotifierType::TELEGRAM;

        $this->postJson($this->route(), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);
    }

    public function testSendInvitationToJoinChatBotSuccess(): void
    {
        Notification::fake();

        $this->formData['notifier_type'] = NotifierType::TELEGRAM;

        $response = $this->postJson($this->route(), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        $person = Person::query()->findOrFail($response->json('id'));

        Notification::assertSentTo($person, InvitationToJoinChatBot::class);
    }

    public function testMarkThatInviteToJoinChatBotHasBeenSentSuccess(): void
    {
        $this->formData['notifier_type'] = NotifierType::TELEGRAM;

        $response = $this->postJson($this->route(), $this->formData)
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

        $this->postJson($this->route(), $this->formData)
            ->assertOk()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(Person::class, $this->formData);
    }

    #[DataProvider('uniqueFieldsProvider')]
    public function testUniqueFields(string $field, string $expectedMessage): void
    {
        $this->formData[$field] = $this->personFactory->createOne()->getAttribute($field);

        $this->postJson($this->route(), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $expectedMessage]);
    }

    public static function uniqueFieldsProvider(): array
    {
        return [
            ['email', 'The email has already been taken'],
            ['phone_number', 'The phone number has already been taken']
        ];
    }

    #[DataProvider('requiredFieldsIfNotificationByProvider')]
    public function testRequiredFieldsIfNotificationBy(NotifierType $notifierType, string $field, string $expectedMessage): void
    {
        $this->formData['notifier_type'] = $notifierType;
        $this->formData[$field] = '';

        $this->postJson($this->route(), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $expectedMessage]);
    }

    public static function requiredFieldsIfNotificationByProvider(): array
    {
        return [
            [NotifierType::EMAIL, 'email', 'The email field is required when notifier type is ' . NotifierType::EMAIL->value],
            [NotifierType::TELEGRAM, 'phone_number', 'The phone number field is required when notifier type is ' . NotifierType::TELEGRAM->value]
        ];
    }

    #[DataProvider('invalidDataProvider')]
    public function testInvalidData(string $field, mixed $invalidValue, string $expectedMessage): void
    {
        $this->formData[$field] = $invalidValue;

        $this->postJson($this->route(), $this->formData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field => $expectedMessage]);
    }

    public static function invalidDataProvider(): array
    {
        return [
            ['first_name', '', 'The first name field is required'],
            ['last_name', '', 'The last name field is required'],
            ['middle_name', '', 'The middle name field is required']
        ];
    }

    public function testInvalidTokenFail(): void
    {
        $invalidToken = fake()->uuid();

        $this->withToken($invalidToken)
            ->postJson($this->route(), $this->formData)
            ->assertUnauthorized();
    }

    public function testGuestFail(): void
    {
        $this->withoutToken()
            ->postJson($this->route(), $this->formData)
            ->assertUnauthorized();
    }

    protected function route(): string
    {
        return route(static::ROUTE_NAME);
    }
}
