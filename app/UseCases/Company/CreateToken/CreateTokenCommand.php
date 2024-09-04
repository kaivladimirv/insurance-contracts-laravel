<?php

declare(strict_types=1);

namespace App\UseCases\Company\CreateToken;

use App\UseCases\Command;
use Spatie\LaravelData\Data;

class CreateTokenCommand extends Data implements Command
{
    public function __construct(
        readonly public int $companyId
    ) {
    }
}
