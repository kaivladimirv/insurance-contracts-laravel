<?php

declare(strict_types=1);

namespace App\Events\Company;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyEmailChanged implements ShouldDispatchAfterCommit
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        readonly public int $companyId,
        readonly public string $newEmail
    ) {
    }
}
