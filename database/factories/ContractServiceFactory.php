<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LimitType;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContractService>
 */
class ContractServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'limit_type' => fake()->randomElement(LimitType::cases()),
            'limit_value' => fake()->randomNumber(1, 50000),
            'service_id' => Service::factory(),
            'contract_id' => Contract::factory(),
        ];
    }

    public function for($factory, $relationship = null): self
    {
        $self =  parent::for($factory, $relationship);

        if ($factory instanceof Contract) {
            return $self->definitionServiceForCompany($factory->company);
        }

        if ($factory instanceof Service) {
            return $self->definitionContractForCompany($factory->company);
        }

        return $self;
    }

    private function definitionServiceForCompany(Company $company): self
    {
        return $this->state(function ($attributes) use ($company) {
            $definition = [];

            if ($attributes['service_id'] instanceof Factory) {
                $definition['service_id'] = Service::factory()->for($company);
            }

            return $definition;
        });
    }

    private function definitionContractForCompany(Company $company): self
    {
        return $this->state(function ($attributes) use ($company) {
            $definition = [];

            if ($attributes['contract_id'] instanceof Factory) {
                $definition['contract_id'] = Contract::factory()->for($company);
            }

            return $definition;
        });
    }

    public function withLimitSum(?float $value = null): self
    {
        $definition = ['limit_type' => LimitType::SUM];

        if ($value !== null) {
            $definition['limit_value'] = $value;
        }

        return $this->state(fn() => $definition);
    }

    public function withLimitQuantity(?float $value = null): self
    {
        $definition = ['limit_type' => LimitType::QUANTITY];

        if ($value !== null) {
            $definition['limit_value'] = $value;
        }

        return $this->state(fn() => $definition);
    }
}
