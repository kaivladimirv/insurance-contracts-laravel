<?php

declare(strict_types=1);

namespace App\Providers;

use App\Specifications\CanProvidedServiceBeRegisteredSpecification;
use App\Specifications\ContractIsValidSpecification;
use App\Specifications\DateOfServiceIsIncludedInContractPeriodSpecification;
use App\Specifications\MaxAmountUnderContractIsNotExceededSpecification;
use App\Specifications\ServiceDoesNotExceedLimitSpecification;
use App\Specifications\ServiceIsCoveredSpecification;
use App\UseCases\ProvidedService\Registration\RegistrationHandler;
use DateTimeImmutable;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;

class ProvidedServiceServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->when(RegistrationHandler::class)
            ->needs(SpecificationInterface::class)
            ->give(fn() => $this->app->make(CanProvidedServiceBeRegisteredSpecification::class));

        $this->app->when(CanProvidedServiceBeRegisteredSpecification::class)
            ->needs(SpecificationInterface::class)
            ->give(function (Application $app) {
                return [
                    $app->make(ContractIsValidSpecification::class),
                    $app->make(ServiceIsCoveredSpecification::class),
                    $app->make(DateOfServiceIsIncludedInContractPeriodSpecification::class),
                    $app->make(ServiceDoesNotExceedLimitSpecification::class),
                    $app->make(MaxAmountUnderContractIsNotExceededSpecification::class)
                ];
            });

        $currentDate = new DateTimeImmutable();
        $this->app->when(ContractIsValidSpecification::class)
            ->needs('$date')
            ->give($currentDate);
    }

    public function provides(): array
    {
        return [
            RegistrationHandler::class,
            CanProvidedServiceBeRegisteredSpecification::class,
            ContractIsValidSpecification::class
        ];
    }
}
