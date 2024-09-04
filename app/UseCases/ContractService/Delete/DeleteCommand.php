<?php

declare(strict_types=1);

namespace App\UseCases\ContractService\Delete;

use App\UseCases\Command;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;

class DeleteCommand extends Data implements Command
{
    public function __construct(
        #[FromRouteParameter('contract_id')]
        readonly public int $contract_id,
        #[FromRouteParameter('service_id')]
        readonly public int $service_id
    ) {
    }
}
