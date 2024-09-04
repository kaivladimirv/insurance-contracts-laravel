<?php

declare(strict_types=1);

namespace App\Http;

use App\Models\Company;

class Request extends \Illuminate\Http\Request
{
    public function company($guard = null): ?Company
    {
        return $this->user($guard);
    }
}
