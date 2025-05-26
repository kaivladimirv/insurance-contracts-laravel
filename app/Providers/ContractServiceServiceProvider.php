<?php

declare(strict_types=1);

namespace App\Providers;

use App\Specifications\ContractServiceNotYetProvidedSpecification;
use App\UseCases\ContractService\Update\UpdateHandler;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ContractServiceServiceProvider extends ServiceProvider implements DeferrableProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->when(UpdateHandler::class)
            ->needs(SpecificationInterface::class)
            ->give(fn() => $this->app->make(ContractServiceNotYetProvidedSpecification::class));
    }

    #[Override]
    public function provides(): array
    {
        return [
            UpdateHandler::class
        ];
    }
}
