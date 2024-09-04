<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LimitType;
use App\Models\ContractService;
use App\Models\ProvidedService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<ProvidedService>
 */
class ProvidedServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date_of_service' => fake()->date(),
            'service_name' => fake()->unique()->sentence(),
            'limit_type' => fake()->randomElement(LimitType::cases()),
            'quantity' => fake()->numberBetween(1, 20),
            'price' => fake()->randomFloat(0, 1, 20000),
        ];
    }

    public function for($factory, $relationship = null): self
    {
        if ($this->isContractService($factory)) {
            return $this->forContractService($factory);
        }

        return parent::for($factory, $relationship);
    }

    private function isContractService(Factory|Model $factory): bool
    {
        return ($factory instanceof ContractServiceFactory or $factory instanceof ContractService);
    }

    private function forContractService(Factory|ContractService $factory): self
    {
        return $this->state(function () use ($factory) {
            $contractService = ($factory instanceof Factory ? $factory->create() : $factory);

            return [
                'company_id' => $contractService->contract->company,
                'contract_id' => $contractService->contract,
                'service_id' => $contractService->service,
                'service_name' => $contractService->service->name,
                'limit_type' => $contractService->limit_type
            ];
        });
    }

    public function configure(): static
    {
        return $this->afterMaking(function (ProvidedService $providedService) {
            $providedService->recalcAmount();

            if ($providedService->insuredPerson) {
                $providedService->contract_id = $providedService->insuredPerson->contract_id;
            }
        });
    }

    public function withDateOfServiceBetween(string $startDate, string $endDate): self
    {
        return $this->state(
            fn() => ['date_of_service' => fake()->dateTimeBetween($startDate, $endDate)->format('Y-m-d')]
        );
    }

    public function withQuantityBetween(int $from, int $to): self
    {
        return $this->state(
            fn() => ['quantity' => fake()->numberBetween($from, $to)]
        );
    }
}
