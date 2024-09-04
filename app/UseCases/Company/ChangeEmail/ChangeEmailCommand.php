<?php

declare(strict_types=1);

namespace App\UseCases\Company\ChangeEmail;

use App\UseCases\Command;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

#[Schema(
    title: 'ChangeEmailCommand',
    required: ['email'],
    properties: [
        new Property(
            property: 'email',
            description: 'Email',
            type: 'string',
            format: 'email',
            default: '',
            maxLength: 255,
            example: 'tester@test.app'
        )
    ]
)]
class ChangeEmailCommand extends Data implements Command
{
    public function __construct(
        readonly public int $company_id,
        #[Max(255)]
        #[Email([Email::RfcValidation, Email::DnsCheckValidation])]
        readonly public string $email
    ) {
    }
}
