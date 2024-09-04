<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LimitType;
use App\Models\Balance;
use App\Models\ContractService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Balance>
 */
class BalanceFactory extends Factory
{
    private const int QUANTITY_MIN = 1;
    private const int QUANTITY_MAX = 20;
    private const float AMOUNT_MIN = 1;
    private const float AMOUNT_MAX = 20000;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var LimitType $limitType */
        $limitType = fake()->randomElement(LimitType::cases());

        if ($limitType->isItQuantityLimiter()) {
            $balance = fake()->numberBetween(self::QUANTITY_MIN, self::QUANTITY_MAX);
        } else {
            $balance = fake()->randomFloat(0, self::AMOUNT_MIN, self::AMOUNT_MAX);
        }

        return [
            'limit_type' => $limitType,
            'balance' => $balance
        ];
    }

    public function withQuantityBalanceBetween(float $min = self::QUANTITY_MIN, float $max = self::QUANTITY_MAX): self
    {
        return $this->state(
            [
                'limit_type' => LimitType::QUANTITY,
                'balance' => fake()->numberBetween($min, $max)
            ]
        );
    }

    public function withAmountBalanceBetween(float $min = self::AMOUNT_MIN, float $max = self::AMOUNT_MAX): self
    {
        return $this->state(
            [
                'limit_type' => LimitType::SUM,
                'balance' => fake()->randomFloat(0, $min, $max)
            ]
        );
    }

    public function for($factory, $relationship = null): self
    {
        if ($factory instanceof ContractService) {
            return $this->forContractService($factory);
        }

        return parent::for($factory, $relationship);
    }

    private function forContractService(ContractService $contractService): self
    {
        return $this->state(function () use ($contractService) {
            return [
                'contract_id' => $contractService->contract_id,
                'service_id' => $contractService->service_id,
                'limit_type' => $contractService->limit_type,
                'balance' => $contractService->limit_value
            ];
        });
    }
}
