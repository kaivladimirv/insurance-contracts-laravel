<?php

declare(strict_types=1);

namespace App\Facades;

use App\Models\Company;
use Illuminate\Contracts\Auth\Authenticatable;

class Auth extends \Illuminate\Support\Facades\Auth
{
    public static function company(): Authenticatable|Company
    {
        return self::user();
    }
}
