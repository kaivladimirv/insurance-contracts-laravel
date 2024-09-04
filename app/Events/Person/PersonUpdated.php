<?php

declare(strict_types=1);

namespace App\Events\Person;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PersonUpdated implements ShouldDispatchAfterCommit
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        readonly public int $personId,
        readonly public bool $hasNotifierTypeChanged,
        readonly public bool $hasPhoneNumberChanged
    ) {
    }
}
