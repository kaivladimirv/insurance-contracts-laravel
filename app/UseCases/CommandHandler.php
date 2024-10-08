<?php

declare(strict_types=1);

namespace App\UseCases;

interface CommandHandler
{
    public function handle(Command $command);
}
