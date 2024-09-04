<?php

declare(strict_types=1);

namespace App\Enums;

enum NotifierType: int
{
    case EMAIL = 0;
    case TELEGRAM = 1;
}
