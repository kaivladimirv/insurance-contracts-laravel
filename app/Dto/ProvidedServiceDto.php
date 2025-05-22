<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Support\Carbon;

class ProvidedServiceDto
{
    public function __construct(
        public Carbon $dateOfService,
        public int $serviceId,
        public string $serviceName,
        public float $quantity,
        public float $price,
        public float $amount,
        public int $contractId,
        public int $insuredPersonId,
        public float $value
    ) {
    }
}
