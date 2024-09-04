<?php

declare(strict_types=1);

namespace App\UseCases\Contract\Delete;

use App\UseCases\Command;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;

class DeleteCommand extends Data implements Command
{
    public function __construct(
        readonly public int $company_id,
        #[FromRouteParameter('id')]
        readonly public int $id
    ) {
    }
}
