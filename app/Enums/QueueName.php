<?php

declare(strict_types=1);

namespace App\Enums;

enum QueueName: string
{
    case Default = 'default';
    case Emails = 'emails';
    case Balances = 'balances';
}
