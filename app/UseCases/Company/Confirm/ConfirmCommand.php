<?php

declare(strict_types=1);

namespace App\UseCases\Company\Confirm;

use App\UseCases\Command;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

#[PathParameter(
    parameter: 'emailConfirmToken',
    name: 'emailConfirmToken',
    description: 'Email confirm token',
    required: true,
    schema: new Schema(type: 'string', maxLength: 255)
)]
class ConfirmCommand extends Data implements Command
{
    public function __construct(
        #[FromRouteParameter('emailConfirmToken')]
        #[Max(255)]
        readonly public string $emailConfirmToken
    ) {
    }
}
