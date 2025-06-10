<?php

declare(strict_types=1);

namespace App\UseCases\ProvidedService\Registration;

use App\Events\ProvidedService\ProvidedServiceRegistered;
use App\Models\Builders\ProvidedServiceBuilder;
use App\UseCases\AbstractHandler;
use App\UseCases\Command;
use Kaivladimirv\LaravelSpecificationPattern\SpecificationInterface;
use Override;

readonly class RegistrationHandler extends AbstractHandler
{
    /**
     * @psalm-api
     */
    public function __construct(private SpecificationInterface $specification, private ProvidedServiceBuilder $builder)
    {
    }

    #[Override]
    public function handle(RegistrationCommand|Command $command): int
    {
        $providedService = $this->builder->buildFromCommand($command);

        $this->specification->throwExceptionIfIsNotSatisfiedBy($providedService);

        $providedService->save();

        ProvidedServiceRegistered::dispatch($providedService->toDto());

        return $providedService->id;
    }
}
