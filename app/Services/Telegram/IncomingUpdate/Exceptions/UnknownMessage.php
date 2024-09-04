<?php

declare(strict_types=1);

namespace App\Services\Telegram\IncomingUpdate\Exceptions;

use Exception;

class UnknownMessage extends Exception
{
    protected $message = 'Unknown message';
}
