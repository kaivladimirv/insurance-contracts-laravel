<?php

declare(strict_types=1);

namespace App\UseCases\Person\Add;

use App\Enums\NotifierType;
use App\Models\Person;
use App\UseCases\Command;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[Schema(
    schema: 'PersonAddCommand',
    title: 'PersonAddCommand',
    required: ['last_name', 'first_name', 'middle_name'],
    properties: [
        new Property(
            property: 'last_name',
            description: 'Last name',
            type: 'string',
            maxLength: 255,
            example: 'Ivanov'
        ),
        new Property(
            property: 'first_name',
            description: 'First name',
            type: 'string',
            maxLength: 255,
            example: 'Ivan'
        ),
        new Property(
            property: 'middle_name',
            description: 'Middle name',
            type: 'string',
            maxLength: 255,
            example: 'Ivanovich'
        ),
        new Property(
            property: 'email',
            description: 'Email',
            type: 'string',
            maxLength: 255,
            example: 'ivanov_ivan_91@gmail.com'
        ),
        new Property(
            property: 'phone_number',
            description: 'Phone number',
            type: 'string',
            maxLength: 15,
            example: '7773339011'
        ),
        new Property(
            property: 'notifier_type',
            description: 'Notifier type (0 - on email, 1 - on telegram)',
            type: 'integer',
            enum: NotifierType::class,
            example: 1
        )
    ]
)]
class AddCommand extends Data implements Command
{
    public function __construct(
        readonly public int $company_id,
        #[Max(255)]
        readonly public string $last_name,
        #[Max(255)]
        readonly public string $first_name,
        #[Max(255)]
        readonly public string $middle_name,
        readonly public ?string $email,
        readonly public ?string $phone_number,
        readonly public ?int $notifier_type
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'email' => [
                'nullable',
                'max:255',
                'email:rfc,dns',
                'required_if:notifier_type,' . NotifierType::EMAIL->value,
                Rule::unique(Person::class, 'email')->where('company_id', $context->payload['company_id'])
            ],
            'phone_number' => [
                'nullable',
                'max:15',
                'required_if:notifier_type,' . NotifierType::TELEGRAM->value,
                Rule::unique(Person::class, 'phone_number')->where('company_id', $context->payload['company_id'])
            ],
            'notifier_type' => [
                'nullable',
                new Enum(NotifierType::class)
            ]
        ];
    }
}
