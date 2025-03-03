<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'company_id' => Company::factory()
        ];
    }
}
