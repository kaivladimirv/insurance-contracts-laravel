<?php

declare(strict_types=1);

namespace App\Providers;

use App\Specifications\PersonIsNotInUseAsInsuredSpecification;
use App\UseCases\Person\Delete\DeleteHandler;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PersonServiceProvider extends ServiceProvider implements DeferrableProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->when(DeleteHandler::class)
            ->needs(SpecificationInterface::class)
            ->give(fn() => $this->app->make(PersonIsNotInUseAsInsuredSpecification::class));
    }

    #[Override]
    public function provides(): array
    {
        return [
            DeleteHandler::class
        ];
    }
}
