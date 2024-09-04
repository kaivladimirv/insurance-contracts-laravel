<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\NotifierType;
use App\Enums\TelegramChatStatus;
use App\Models\Company;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'last_name' => fake()->unique()->lastName(),
            'first_name' => fake()->unique()->firstName(),
            'middle_name' => fake()->unique()->lastName(),
            'email' => fake()->unique()->freeEmail(),
            'phone_number' => fake()->unique()->e164PhoneNumber(),
            'notifier_type' => fake()->optional()->randomElement(NotifierType::cases()),
            'company_id' => Company::factory()
        ];
    }

    public function withInvitationSent(): self
    {
        return $this->state(
            [
                'telegram_chat_invite_token' => fake()->uuid(),
                'telegram_invite_date_for_chat' => Carbon::parse(fake()->dateTime()),
                'telegram_chat_status' => TelegramChatStatus::INVITATION_SENT
            ]
        );
    }

    public function withJoinedToChatbot(): self
    {
        return $this->state(
            [
                'telegram_chat_id' => fake()->randomNumber(),
                'telegram_chat_status' => TelegramChatStatus::JOINED
            ]
        );
    }
}
