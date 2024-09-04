<?php

declare(strict_types=1);

namespace App\UseCases\Company\ChangePassword;

use App\UseCases\Command;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

#[Schema(
    title: 'ChangePasswordCommand',
    required: ['password', 'password_confirmation'],
    properties: [
        new Property(
            property: 'password',
            description: 'Password',
            type: 'string',
            format: 'password',
            default: '',
            maxLength: 255,
            minLength: 8,
            example: '123b5-7A11'
        ),
        new Property(
            property: 'password_confirmation',
            description: 'Password confirmation',
            type: 'string',
            format: 'password',
            default: '',
            maxLength: 255,
            minLength: 8,
            example: '123b5-7A11'
        )
    ]
)]
class ChangePasswordCommand extends Data implements Command
{
    public function __construct(
        readonly public int $company_id,
        #[Max(255)]
        #[Password(min: 8, letters: true, mixedCase: true, numbers: true, symbols: true, uncompromised: true)]
        #[Confirmed]
        readonly public string $password
    ) {
    }
}
