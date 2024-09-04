<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTime();
        $endDate = fake()->dateTimeBetween($startDate, '+ 1 year');

        return [
            'number' => fake()->unique()->numberBetween(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'max_amount' => fake()->randomFloat(0, 10000, 9999999),
            'company_id' => Company::factory()
        ];
    }

    public function withCurrentYear(): self
    {
        return $this->state(function () {

            return [
                'start_date' => date('Y-m-d', strtotime('first day of january this year')),
                'end_date' => date('Y-m-d', strtotime('last day of december this year'))
            ];
        });
    }

    public function withPreviousYear(): self
    {
        return $this->state(function () {

            return [
                'start_date' => now()->subYear()->startOfYear()->toDateString(),
                'end_date' => now()->subYear()->endOfYear()->toDateString()
            ];
        });
    }
}
