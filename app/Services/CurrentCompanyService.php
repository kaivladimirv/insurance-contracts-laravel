<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

class CurrentCompanyService
{
    private ?int $companyId = null;

    public function setCompanyId(?int $companyId): void
    {
        if (($companyId !== null) and ($companyId <= 0)) {
            throw new InvalidArgumentException(__('Company ID must be positive'));
        }

        $this->companyId = $companyId;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function hasCompany(): bool
    {
        return $this->companyId !== null;
    }
}
