<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Contract;
use App\Models\InsuredPerson;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InsuredPerson>
 */
class InsuredPersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'policy_number' => fake()->unique()->regexify('[A-Za-z0-9]{20}'),
            'is_allowed_to_exceed_limit' => fake()->boolean(),
            'person_id' => Person::factory(),
            'contract_id' => Contract::factory()
        ];
    }

    public function withAllowedToExceedLimit(): self
    {
        return $this->state(fn() => ['is_allowed_to_exceed_limit' => true]);
    }

    public function withNotAllowedToExceedLimit(): self
    {
        return $this->state(fn() => ['is_allowed_to_exceed_limit' => false]);
    }
}
