<?php

declare(strict_types=1);

namespace App\Providers;

use App\Specifications\ContractHasNoProvidedServicesSpecification;
use App\UseCases\Contract\Delete\DeleteHandler;
use Override;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;

class ContractServiceProvider extends ServiceProvider implements DeferrableProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->when(DeleteHandler::class)
            ->needs(SpecificationInterface::class)
            ->give(fn() => $this->app->make(ContractHasNoProvidedServicesSpecification::class));
    }

    #[Override]
    public function provides(): array
    {
        return [
            DeleteHandler::class
        ];
    }
}
