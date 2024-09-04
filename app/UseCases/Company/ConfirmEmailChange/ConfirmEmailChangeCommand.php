<?php

declare(strict_types=1);

namespace App\UseCases\Company\ConfirmEmailChange;

use App\UseCases\Command;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

#[PathParameter(
    parameter: 'newEmailConfirmToken',
    name: 'newEmailConfirmToken',
    description: 'New email confirm token',
    required: true,
    schema: new Schema(type: 'string', maxLength: 255)
)]
class ConfirmEmailChangeCommand extends Data implements Command
{
    public function __construct(
        readonly public int $company_id,
        #[FromRouteParameter('newEmailConfirmToken')]
        #[Max(255)]
        readonly public string $new_email_confirm_token
    ) {
    }
}
