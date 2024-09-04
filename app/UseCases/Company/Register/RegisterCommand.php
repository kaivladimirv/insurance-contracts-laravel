<?php

declare(strict_types=1);

namespace App\UseCases\Company\Register;

use App\UseCases\Command;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

#[Schema(
    title: 'RegisterCommand',
    required: ['name', 'email', 'password', 'password_confirmation'],
    properties: [
        new Property(
            property: 'name',
            description: 'Company name',
            type: 'string',
            default: '',
            maxLength: 255,
            example: 'Company name #1'
        ),
        new Property(
            property: 'email',
            description: 'Email',
            type: 'string',
            format: 'email',
            default: '',
            maxLength: 255,
            example: 'tester@gmail.com'
        ),
        new Property(
            property: 'password',
            description: 'Password',
            type: 'string',
            format: 'password',
            default: '',
            maxLength: 255,
            minLength: 8,
            example: '123b5-7A1'
        ),
        new Property(
            property: 'password_confirmation',
            description: 'Password confirmation',
            type: 'string',
            format: 'password',
            default: '',
            maxLength: 255,
            minLength: 8,
            example: '123b5-7A1'
        )
    ]
)]
class RegisterCommand extends Data implements Command
{
    public function __construct(
        #[Max(255)]
        #[Unique('companies')]
        readonly public string $name,
        #[Max(255)]
        #[Email([Email::RfcValidation, Email::DnsCheckValidation])]
        #[Unique('companies')]
        readonly public string $email,
        #[Max(255)]
        #[Password(min: 8, letters: true, mixedCase: true, numbers: true, symbols: true, uncompromised: true)]
        #[Confirmed]
        readonly public string $password
    ) {
    }
}
